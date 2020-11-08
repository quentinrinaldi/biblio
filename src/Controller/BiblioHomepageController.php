<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/biblio", name="biblio_")
 */
class BiblioHomepageController extends AbstractController
{
    /**
     * @Route("/homepage", name="homepage")
     */
    public function biblioIndex(BookRepository $bookRepository, CategoryRepository $categoryRepository)
    {
        $books = $bookRepository->findAll();

        return $this->render('frontEnd/biblio/homepage/index.html.twig', [
            'books' => $books,
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        return $this->render('frontEnd/biblio/homepage/about.html.twig', [
        ]);
    }

    public function getPinnedBooksSelection(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findPinnedAndAvailableDocumentsSelection(10);

        return $this->render('frontEnd/shared/_homepage_selection.html.twig', [
            'documentsSelection' => $books,
            'selectionName' => 'Our selection',
        ]);
    }

    public function getNewBooksSelection(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findNewestAvailableDocumentsSelection(10);

        return $this->render('frontEnd/shared/_homepage_selection.html.twig', [
            'documentsSelection' => $books,
            'selectionName' => 'Just arrived',
        ]);
    }

    public function getMostPopularBooksSelection(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findMostPopularAvailableDocumentsSelection(10);

        return $this->render('frontEnd/shared/_homepage_selection.html.twig', [
            'documentsSelection' => $books,
            'selectionName' => 'Most Popular',
        ]);
    }
}
