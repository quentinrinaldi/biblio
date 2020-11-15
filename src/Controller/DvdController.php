<?php

namespace App\Controller;

use App\Entity\Borrowing;
use App\Entity\Copy;
use App\Entity\Dvd;
use App\Entity\User;
use App\Event\DocumentBorrowedEvent;
use App\Event\DocumentReturnedEvent;
use App\Policy\PenaltyPolicy;
use App\Repository\CopyRepository;
use App\Repository\DvdRepository;
use DateTime;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/movio/dvd", name="movio_dvd_")
 */
class DvdController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(DvdRepository $dvdRepository)
    {
        $dvds = $dvdRepository->findAll();

        return $this->render('frontEnd/movio/dvd/index.html.twig', [
            'dvds' => $dvds,
            'title' => 'All dvds',
            'websiteFlag' => 'movio',
        ]);
    }

    /**
     * @Route("/{id}", name="show")
     * Page which show a dvd
     */
    public function show(DvdRepository $dvdRepository, Dvd $dvd)
    {
        $alreadyBorrowed = false;
        if ($user = $this->getUser()) {
            //See if the document is already borrowed by the authentificated user
            $userDocumentBorrowings = $dvdRepository->findIfDocumentIsBorrowedByUser($user, $dvd);
            $alreadyBorrowed = $userDocumentBorrowings;
            if (!empty($userDocumentBorrowings)) {
                $alreadyBorrowed = true;
            }
        }
        $remainingCopies = $dvdRepository->getRemainingCopiesCountOfDocument($dvd);
        $categories = $dvd->getCategories();

        return $this->render('frontEnd/movio/dvd/show.html.twig', [
            'document' => $dvd,
            'remainingCopies' => $remainingCopies,
            'alreadyBorrowed' => $alreadyBorrowed,
            'activeCategories' => $categories,
        ]);
    }

    /**
     * @Route("/{id}/borrow", name="borrow")
     * Route which allow the authentificated user to borrow a dvd
     */
    public function borrow(DvdRepository $dvdRepository, CopyRepository $copyRepository, EventDispatcherInterface $dispatcher, Dvd $dvd)
    {
        try {
            $this->
            {$this}->denyAccessUnlessGranted('document', $dvd, 'This document is no longer available');
        } catch (\Exception $e) {
            $this->addFlash('error', 'This document is no longer available!');

            return $this->redirectToRoute('dvd_show', ['id' => $dvd->getId()]);
        }

        try {
            $this->denyAccessUnlessGranted('borrow', $dvd, 'You are not allowed to borrow this dvd');
        } catch (\Exception $e) {
            $this->addFlash('error', 'You are not allowed to borrow this dvd ! You may be not authenticated, have current exclusion, or you\'ve reached the borrowing limit.');

            return $this->redirectToRoute('movio_dvd_show', ['id' => $dvd->getId()]);
        }

        $copie = $copyRepository->findFirstAvailableCopy($dvd);
        $borrowing = new Borrowing();
        $borrowing->setCreatedAt(new Datetime('now'));
        $borrowing->setUser($this->getUser());

        $expectedReturnDate = new DateTime('now');
        $expectedReturnDate->add(new \DateInterval('P14D'));

        $borrowing->setExpectedReturnDate($expectedReturnDate);
        $borrowing->setStatus(Borrowing::STATUS_READING_IN_PROGRESS);

        $copie->setStatus(Copy::STATUS_BORROWED);
        $borrowing->setCopy($copie);
        $em = $this->getDoctrine()->getManager();
        $em->persist($borrowing);
        $em->flush();

        $this->addFlash('success', 'Your borrowing has been successful');

        $event = new DocumentBorrowedEvent($dvd);
        $dispatcher->dispatch($event);

        return $this->redirectToRoute('movio_dvd_show', ['id' => $dvd->getId()]);
    }

    /**
     * @Route("/{id}/return", name="return")
     * method which do return a document
     */
    public function return(EventDispatcherInterface $dispatcher, PenaltyPolicy $penaltyPolicy, Borrowing $borrowing)
    {
        $result = $penaltyPolicy->doReturnDocument($borrowing);
        $this->addFlash($result['status'], $result['message']);
        $event = new DocumentReturnedEvent($borrowing->getCopy()->getDocument());
        $dispatcher->dispatch($event);

        return $this->redirectToRoute('account_borrowing');
    }
}
