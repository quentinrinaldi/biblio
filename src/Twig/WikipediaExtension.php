<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\Artist;
use App\Entity\Book;
use App\Entity\Document;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class WikipediaExtension extends AbstractExtension
{

    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getFilters()
    {
        return [
            // the logic of this filter is now implemented in a different class
            new TwigFilter('wp', [$this, 'getWikipediaPage']),
        ];
    }

    /**
     * @param Artist $artist
     * @return bool|string
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @todo Tester INFO
     */
    public function getWikipediaPage(Artist $artist)
    {
        try {
            $url = "https://fr.wikipedia.org/wiki/{$artist->getFirstName()}_{$artist->getLastName()}";
            $response = $this->httpClient->request(
                'INFO',
                $url
            );
            if ($response->getStatusCode() == 200)
                return $url;
        }
        //Prevent connexion exception
        catch (\Exception $exception) {
            return false;
        }

        return false;
    }
}