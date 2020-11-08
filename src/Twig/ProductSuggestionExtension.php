<?php

// src/Twig/AppExtension.php

namespace App\Twig;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProductSuggestionExtension extends AbstractExtension
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('suggestion', [$this, 'suggestion']),
        ];
    }

    public function suggestion(array $documents, int $nbMax, int $step)
    {
        $docLength = count($documents);
        $rangeMax = $step + $nbMax;
        if ($rangeMax >= $docLength) {
            $nbMax = $docLength - $step;
        }

        return array_slice($documents, $step, $nbMax);
    }
}
