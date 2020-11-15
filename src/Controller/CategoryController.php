<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Document;
use App\Entity\Dvd;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Repository\DvdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("biblio/category/{id}/book", name="category_book_show")
     * Show all books with a given category
     */
    public function showBooks(BookRepository $booksRepository, Category $category): Response
    {
        $books = $booksRepository->findDocumentsByCategory($category);

        return $this->render('frontEnd/shared/document/index.html.twig', [
            'documents' => $books,
            'websiteFlag' => 'biblio',
            'activeCategories' => [$category],
            'title' => "All books of {$category}",
        ]);
    }

    /**
     * @Route("movio/category/{id}/dvd", name="category_dvd_show")
     * Show all books with a given category
     */
    public function showDvds(DvdRepository $dvdRepository, Category $category): Response
    {
        $dvds = $dvdRepository->findDocumentsByCategory($category);

        return $this->render('frontEnd/shared/document/index.html.twig', [
            'documents' => $dvds,
            'activeCategories' => [$category],
            'websiteFlag' => 'movio',
            'title' => "All books of {$category}",
        ]);
    }

    /**
     * Return a partial template which contain the category toolbar, with the active category if specified.
     *
     * @param null|mixed $activeCategories
     */
    public function biblioCategoriesToolbar(CategoryRepository $categoryRepository, $activeCategories = null): Response
    {
        $categories = $categoryRepository->findAllCategoriesNamesOfBooks();

        return $this->render('frontEnd/category/_categories_index.html.twig', [
            'categories' => $categories,
            'activeCategories' => $activeCategories,
            'everyItemsPath' => $this->generateUrl('biblio_book_index'),
            'everyItemsTitle' => 'All Books',
        ]);
    }

    /**
     * Return a partial template which contain the category toolbar, with the active category if specified.
     *
     * @param null|mixed $activeCategories
     */
    public function movioCategoriesToolbar(CategoryRepository $categoryRepository, $activeCategories = null): Response
    {
        $categories = $categoryRepository->findAllCategoriesNamesOfDvds();

        return $this->render('frontEnd/category/_categories_index.html.twig', [
            'categories' => $categories,
            'activeCategories' => $activeCategories,
            'everyItemsPath' => $this->generateUrl('movio_dvd_index'),
            'everyItemsTitle' => 'All dvds',
        ]);
    }

    /**
     * Given a document, look at all its categories and then suggest some documents from at least one of those categories.
     *
     * @return Response containing a partiel template
     */
    public function getDocumentsSamplesByCategories(CategoryRepository $categoryRepository, Document $document): Response
    {
        $repo = null;
        if ($document instanceof Book) {
            $repo = $this->getDoctrine()->getManager()->getRepository(Book::class);
        } elseif ($document instanceof Dvd) {
            $repo = $this->getDoctrine()->getManager()->getRepository(Dvd::class);
        }
        $categories = $categoryRepository->findAllCategoriesIdByDocument($document);
        $documents = $repo->findDocumentsByCategories($categories, 4);

        return $this->render('frontEnd/shared/document/_documents_items_partial.html.twig', [
            'documents' => $documents,
        ]);
    }
}
