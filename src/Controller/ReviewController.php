<?php

namespace App\Controller;

use App\Document\Review;
use App\Form\ReviewFormType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/review", name="review_")
 */
class ReviewController extends AbstractController
{
    /**
     * @Route("/document/id", name="index")
     */
    public function index(DocumentManager $dm, int $id): Response
    {
        $reviews = $dm->getRepository(Review::class)->findBy(['documentId' => $id]);

        return $this->render('frontEnd/review/index.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    /**
     * @Route("/document/{id}/new", name="new")
     */
    public function new(Request $request, DocumentManager $dm, int $id): Response
    {
        if (!$this->getUser()) {
            return $this->render('frontEnd/review/anonymous_template.html.twig', []);
        }
        $user = $this->getUser();
        $review = new Review();
        $review->setDocumentId($id);
        $review->setUserId($user->getId());

        $form = $this->createForm(ReviewFormType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dm->persist($review);
            $dm->flush();

            return $this->render('frontEnd/review/confirmed.html.twig', []);
        }

        return $this->render('frontEnd/review/new.html.twig', [
            'review' => $review,
            'documentId' => $id,
            'form' => $form->createView(),
        ]);
    }
}
