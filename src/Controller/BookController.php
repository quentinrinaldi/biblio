<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Borrowing;
use App\Entity\Copy;
use App\Entity\User;
use App\Event\DocumentBorrowedEvent;
use App\Event\DocumentReturnedEvent;
use App\Policy\PenaltyPolicy;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Repository\CopyRepository;
use DateTime;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/biblio/book", name="biblio_book_")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(BookRepository $bookRepository)
    {
        $books = $bookRepository->findAll();

        return $this->render('frontEnd/shared/document/index.html.twig', [
            'documents' => $books,
            'title' => 'All books',
        ]);
    }

    /**
     * @Route("/{id}", name="show")
     * Page which present a book
     */
    public function show(BookRepository $bookRepository, CategoryRepository $categoryRepository, Book $book)
    {
        $alreadyBorrowed = false;
        if ($user = $this->getUser()) {
            //See if the document is already borrowed by the authentificated user
            $userDocumentBorrowings = $bookRepository->findIfDocumentIsBorrowedByUser($user, $book);
            $alreadyBorrowed = $userDocumentBorrowings;
            if (!empty($userDocumentBorrowings)) {
                $alreadyBorrowed = true;
            }
        }
        $remainingCopies = $bookRepository->getRemainingCopiesCountOfDocument($book);
        $categories = $book->getCategories();

        return $this->render('frontEnd/biblio/book/show.html.twig', [
            'book' => $book,
            'remainingCopies' => $remainingCopies,
            'alreadyBorrowed' => $alreadyBorrowed,
            'activeCategories' => $categories,
        ]);
    }

    /**
     * @Route("/{id}/borrow", name="borrow")
     * Route which allow the authentificated user to borrow a book
     */
    public function borrow(BookRepository $bookRepository, CopyRepository $copyRepository, EventDispatcherInterface $dispatcher, Book $book)
    {
        try {
            $this->
            {$this}->denyAccessUnlessGranted('document', $book, 'This document is no longer available');
        } catch (\Exception $e) {
            $this->addFlash('error', 'This document is no longer available!');

            return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
        }

        try {
            $this->denyAccessUnlessGranted('borrow', $book, 'You are not allowed to borrow this book');
        } catch (\Exception $e) {
            $this->addFlash('error', 'You are not allowed to borrow this book ! You may be not authenticated, have current exclusion, or you\'ve reached the borrowing limit.');

            return $this->redirectToRoute('biblio_book_show', ['id' => $book->getId()]);
        }

        $copie = $copyRepository->findFirstAvailableCopy($book);
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

        $event = new DocumentBorrowedEvent($book);
        $dispatcher->dispatch($event);

        return $this->redirectToRoute('biblio_book_show', ['id' => $book->getId()]);
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
