<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/homepage", name="homepage")
     */
    public function index()
    {
        $books = [
            ['title' => 'Harry Potter et la chambre des secrets', 'stock' => 2, 'synopsis' => "Premier tome", 'stars' => 5],
            ['title' => 'Harry Potter et la coupe de feu', 'stock' => 5, 'synopsis' => "", 'stars' => 3]
        ];
        return $this->render('homepage/index.html.twig', [
            'date' => new \DateTime(),
            'books' => $books
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        return $this->render('homepage/about.html.twig', [
        ]);
    }
}
