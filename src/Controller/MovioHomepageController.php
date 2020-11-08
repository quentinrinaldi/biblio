<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\DvdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/movio", name="movio_")
 */
class MovioHomepageController extends AbstractController
{
    /**
     * @Route("/homepage", name="homepage")
     */
    public function movioIndex(DvdRepository $dvdRepository, CategoryRepository $categoryRepository)
    {
        return $this->render('frontEnd/movio/homepage/index.html.twig', []);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        return $this->render('frontEnd/biblio/homepage/about.html.twig', [
        ]);
    }

    public function getPinnedDvdsSelection(DvdRepository $dvdRepository): Response
    {
        $dvds = $dvdRepository->findPinnedAndAvailableDocumentsSelection(10);
        dump($dvds);

        return $this->render('frontEnd/shared/_homepage_selection.html.twig', [
            'documentsSelection' => $dvds,
            'selectionName' => 'Our selection',
        ]);
    }

    public function getNewDvdsSelection(DvdRepository $dvdRepository): Response
    {
        $dvds = $dvdRepository->findNewestAvailableDocumentsSelection(10);

        return $this->render('frontEnd/shared/_homepage_selection.html.twig', [
            'documentsSelection' => $dvds,
            'selectionName' => 'Just arrived',
        ]);
    }

    public function getMostPopularDvdsSelection(DvdRepository $dvdRepository): Response
    {
        $dvds = $dvdRepository->findMostPopularAvailableDocumentsSelection(10);

        return $this->render('frontEnd/shared/_homepage_selection.html.twig', [
            'documentsSelection' => $dvds,
            'selectionName' => 'Most Popular',
        ]);
    }
}
