<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Category;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/{id}/book", name="book_show")
     * Show all books with a given category
     */
    public function book_show(BookRepository $booksRepository, Category $category): Response
    {
        $books = $booksRepository->findDocumentsByCategory($category);

        return $this->render('frontEnd/biblio/book/index.html.twig', [
            'books' => $books,
            'activeCategories' => [$category],
            'title' => "All books of {$category}",
        ]);
    }

    /**
     * Return a partial template which contain the category toolbar, with the active category if specified.
     *
     * @param null|mixed $activeCategories
     */
    public function categories_toolbar($activeCategories = null, CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAllCategoriesNamesByBook();

        return $this->render('frontEnd/category/_categories_index.html.twig', [
            'categories' => $categories,
            'activeCategories' => $activeCategories,
        ]);
    }

    /**
     * Given a book, look at all its categories and then suggest some books from at least one of those categories.
     *
     * @return Response containing a partiel template
     */
    public function getBooksSamplesByCategories(CategoryRepository $categoryRepository, BookRepository $bookRepository, Book $book): Response
    {
        $categories = $categoryRepository->findAllIdByBook($book);
        $books = $bookRepository->findDocumentsByCategories($categories, 4);

        return $this->render('frontEnd/biblio/book/_books_collection_partial.html.twig', [
            'books' => $books,
        ]);
    }
}
