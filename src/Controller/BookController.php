<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Document;
use App\Entity\Author;

/**
 * @Route("/book", name="book_")
 */
class BookController extends AbstractController
{
    /**
     * @Route(
     *     "/list",
     *     name="list",
     *     methods={"GET"})
     */
    public function index()
    {
        $books = $this->getDoctrine()
            ->getRepository(Document::class)
            ->findAll();
        return $this->render('book/show_all.html.twig',
            ['books' => $books
        ]);
    }

    /**
     * @Route("/{isbn}",
     *     name="show",
     *     requirements={"isbn"="[0-9]{10}"}
     *     )
     */
    public function show(Document $book)
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/list/{author}",
     *     name="list_by_author"
     *     )
     */
    public function getAllBooksByAuthor(Author $author)
    {
        $books = $this->getDoctrine()
            ->getRepository(Document::class)
            ->findByAuthor($author);

        return $this->render('book/show_all_by_author.html.twig', [
            'books' => $books,
            'author' => $author->getName()
        ]);
    }
}
