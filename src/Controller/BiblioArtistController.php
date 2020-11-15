<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Book;
use App\Entity\Document;
use App\Repository\ArtistInvolvementRepository;
use App\Repository\ArtistRepository;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("", name="biblio_artist_")
 */
class BiblioArtistController extends AbstractController
{
    use ArtistController;

    /**
     * @Route("/biblio/artist/role/{roleName}", name="index")
     *
     * @param mixed $roleName
     */
    public function index(ArtistRepository $artistRepository, string $roleName)
    {
        return $this->render('frontEnd/shared/artist/index.html.twig', [
            'artists' => $this->getSortedArtistIndex($artistRepository, $roleName),
            'roleName' => $roleName,
            'website' => 'biblio',
        ]);
    }

    /**
     * @Route("/biblio/artist/{id}", name="show")
     */
    public function show(
        Artist $artist,
        BookRepository $documentRepository,
        ArtistInvolvementRepository $artistInvolvementRepository,
        CategoryRepository $categoryRepository
    ) {
        return $this->render('frontEnd/biblio/artist/show.html.twig', [
            'artist' => $artist,
            'artistData' => $this->getArtistData($artist, $documentRepository, $artistInvolvementRepository, $categoryRepository),
        ]);
    }

    /**
     * @Route("/biblio/artist/{id}/book", name="show_books")
     */
    public function showBooks(Artist $artist, BookRepository $bookRepository)
    {
        $books = $bookRepository->findDocumentsByArtist($artist);

        return $this->render('frontEnd/shared/document/index.html.twig', [
            'documents' => $books,
            'websiteFlag' => 'biblio',
            'title' => "Books written by {$artist->getFirstName()} {$artist->getLastName()}",
        ]);
    }

    //SHOW SOME OTHER DOCUMENTS WHICH HAVE ARTIST IN COMMON WITH A GIVEN DOCUMENT

    public function getBooksSamplesByArtistTemplate(ArtistRepository $artistRepository, BookRepository $bookRepository, Book $document)
    {
        return $this->render('frontEnd/shared/document/_documents_items_partial.html.twig', [
            'documents' => $this->getDocumentsSamplesByArtist($artistRepository, $bookRepository, $document),
        ]);
    }

    //SHOW OTHER DOCUMENTS IN WHICH THE ARTIST IS INVOLVED
    public function getPopularBooksSelectionByArtistTemplate(BookRepository $bookRepository, Artist $artist)
    {
        return $this->render('frontEnd/shared/document/_documents_items_partial.html.twig', [
            'documents' => $this->getPopularBooksSelectionByArtist($bookRepository, $artist),
        ]);
    }

    public function getSimilarArtistsTemplate(Artist $artist, ArtistRepository $artistRepository, CategoryRepository $categoryRepository)
    {
        return $this->render('frontEnd/shared/artist/_artists_suggestion_partial.html.twig', [
            'artists' => $this->getSimilarArtists($artist, $artistRepository, $categoryRepository),
        ]);
    }
}
