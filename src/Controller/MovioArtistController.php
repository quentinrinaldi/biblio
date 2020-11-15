<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Document;
use App\Entity\Dvd;
use App\Repository\ArtistInvolvementRepository;
use App\Repository\ArtistRepository;
use App\Repository\CategoryRepository;
use App\Repository\DocumentRepository;
use App\Repository\DvdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("", name="movio_artist_")
 */
class MovioArtistController extends AbstractController
{
    use ArtistController;

    /**
     * @Route("/movio/artist/role/{roleName}", name="index")
     *
     * @param mixed $roleName
     */
    public function index(ArtistRepository $artistRepository, string $roleName)
    {
        return $this->render('frontEnd/shared/artist/index.html.twig', [
            'artists' => $this->getSortedArtistIndex($artistRepository, $roleName),
            'roleName' => $roleName,
            'website' => 'movio',
        ]);
    }

    /**
     * @Route("/movio/artist/{id}", name="show")
     */
    public function show(
        Artist $artist,
        DocumentRepository $documentRepository,
        ArtistInvolvementRepository $artistInvolvementRepository,
        CategoryRepository $categoryRepository
    ) {
        return $this->render('frontEnd/movio/artist/show.html.twig', [
            'artist' => $artist,
            'artistData' => $this->getArtistData($artist, $documentRepository, $artistInvolvementRepository, $categoryRepository),
        ]);
    }

    /**
     * @Route("/movio/artist/{id}/dvd", name="show_dvds")
     */
    public function showDvds(Artist $artist, DvdRepository $dvdRepository)
    {
        $dvds = $dvdRepository->findDocumentsByArtist($artist);

        return $this->render('frontEnd/biblio/book/index.html.twig', [
            'dvd' => $dvds,
            'title' => "Dvds related to {$artist->getFirstName()} {$artist->getLastName()}",
        ]);
    }

    //SHOW SOME OTHER DOCUMENTS WHICH HAVE ARTIST IN COMMON WITH A GIVEN DOCUMENT

    /**
     * Find documents From the sames artists.
     */
    public function getDvdsSamplesByArtistTemplate(ArtistRepository $artistRepository, DvdRepository $dvdRepository, Dvd $document)
    {
        return $this->render('frontEnd/shared/document/_documents_items_partial.html.twig', [
            'documents' => $this->getDocumentsSamplesByArtist($artistRepository, $dvdRepository, $document),
        ]);
    }

    //SHOW OTHER DOCUMENTS IN WHICH THE ARTIST IS INVOLVED
    public function getPopularDvdsSelectionByArtistTemplate(DvdRepository $dvdRepository, Artist $artist)
    {
        return $this->render('frontEnd/shared/document/_documents_items_partial.html.twig', [
            'documents' => $this->getPopularBooksSelectionByArtist($dvdRepository, $artist),
        ]);
    }

    public function getSimilarArtistsTemplate(Artist $artist, ArtistRepository $artistRepository, CategoryRepository $categoryRepository)
    {
        return $this->render('frontEnd/shared/_artists_suggestion_partial.html.twig', [
            'artists' => $this->getSimilarArtists($artist, $artistRepository, $categoryRepository),
        ]);
    }
}
