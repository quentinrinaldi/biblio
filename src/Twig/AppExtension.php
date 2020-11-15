<?php

// src/Twig/AppExtension.php

namespace App\Twig;

use App\Entity\Artist;
use App\Entity\Book;
use App\Entity\Document;
use App\Entity\Dvd;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function getFilters()
    {
        return [
            // the logic of this filter is now implemented in a different class
            new TwigFilter('generateAdminDocumentUrl', [$this, 'generateDocumentUrl']),
            new TwigFilter('generateDocumentUrl', [$this, 'generateDocumentUrl']),
        ];
    }

    public function getFunctions()
    {
        return [
            // the logic of this filter is now implemented in a different class
            new TwigFunction('generateArtistUrl', [$this, 'generateArtistUrl']),
        ];
    }

    public function generateAdminDocumentUrl(Document $document)
    {
        if ($document instanceof Book) {
            return $this->router->generate('admin_book_show', ['id' => $document->getId()]);
        }
    }

    public function generateDocumentUrl(Document $document)
    {
        if ($document instanceof Book) {
            return $this->router->generate('biblio_book_show', ['id' => $document->getId()]);
        }
        if ($document instanceof Dvd) {
            return $this->router->generate('movio_dvd_show', ['id' => $document->getId()]);
        }
    }

    /**
     * @param [string] $routeFlag : MUST BE 'bilbio' OR 'movio' depending the context
     * @param [Artist] $artist    : The artist we need to link to
     */
    public function generateArtistUrl(string $routeFlag, Artist $artist)
    {
        switch ($routeFlag) {
            case 'biblio':
                return $this->router->generate('biblio_artist_show', ['id' => $artist->getId()]);

            break;
            case 'movio':
                return $this->router->generate('movio_artist_show', ['id' => $artist->getId()]);
        }
    }
}
