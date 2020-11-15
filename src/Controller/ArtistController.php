<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\ArtistInvolvement;
use App\Entity\Document;
use App\Repository\ArtistInvolvementRepository;
use App\Repository\ArtistRepository;
use App\Repository\CategoryRepository;
use App\Repository\DocumentRepository;

trait ArtistController
{
    private function getSortedArtistIndex(ArtistRepository $artistRepository, string $roleName)
    {
        $artists = $artistRepository->findArtistsByRole($roleName);
        $artistsIndex = [];

        //Make an array with first letter as index, like a lexical
        foreach ($artists as $artist) {
            $artistsIndex[$artist->getLastName()[0]][] = $artist;
        }
        //and sort by alphabetical order
        ksort($artistsIndex);

        return $artistsIndex;
    }

    private function getArtistData(
        Artist $artist,
        DocumentRepository $documentRepository,
        ArtistInvolvementRepository $artistInvolvementRepository,
        CategoryRepository $categoryRepository
    ) {
        $documentsCount = $documentRepository->findDocumentsCountByArtist($artist);
        $roles = $artistInvolvementRepository->findDisctinctRolesByArtist($artist);
        $categories = $categoryRepository->findDistinctCategoriesByArtist($artist);

        $artistData['booksCount'] = $documentsCount;
        $artistData['roles'] = $roles;
        $artistData['categories'] = $categories;

        return $artistData;
    }

    //SHOW SOME OTHER DOCUMENTS WHICH HAVE ARTIST IN COMMON WITH A GIVEN DOCUMENT

    /**
     * Find documents From the sames artists.
     */
    private function getDocumentsSamplesByArtist(ArtistRepository $artistRepository, DocumentRepository $documentRepository, Document $document)
    {
        $artists = $artistRepository->findArtistsIdByDocumentAndByRole($document, ArtistInvolvement::TYPE_AUTHOR);

        return $documentRepository->findDocumentsByArtists($artists, 4);
    }

    //SHOW OTHER DOCUMENTS IN WHICH THE ARTIST IS INVOLVED
    private function getPopularBooksSelectionByArtist(DocumentRepository $documentRepository, Artist $artist)
    {
        return $documentRepository->findMostPopularDocumentsByArtist($artist, 4);
    }

    private function getSimilarArtists(Artist $artist, ArtistRepository $artistRepository, CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findDistinctCategoriesByArtist($artist);

        return $artistRepository->findArtistsByCategories($categories);
    }
}
