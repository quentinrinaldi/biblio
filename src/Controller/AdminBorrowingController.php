<?php

namespace App\Controller;

use App\Entity\Borrowing;
use App\Form\AdminBorrowingFormType;
use App\Repository\BorrowingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/borrowing", name="admin_borrowing_")
 */
class AdminBorrowingController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(BorrowingRepository $borrowingRepository): Response
    {
        return $this->render('backEnd/borrowing/index.html.twig', [
            'borrowings' => $borrowingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $borrowing = new Borrowing();
        $form = $this->createForm(AdminBorrowingFormType::class, $borrowing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($borrowing);
            $entityManager->flush();

            return $this->redirectToRoute('admin_borrowing_index');
        }

        return $this->render('backEnd/borrowing/new.html.twig', [
            'borrowing' => $borrowing,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Borrowing $borrowing): Response
    {
        return $this->render('backEnd/borrowing/show.html.twig', [
            'borrowing' => $borrowing,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Borrowing $borrowing): Response
    {
        $form = $this->createForm(AdminBorrowingFormType::class, $borrowing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_borrowing_index');
        }

        return $this->render('backEnd/borrowing/edit.html.twig', [
            'borrowing' => $borrowing,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Borrowing $borrowing): Response
    {
        if ($this->isCsrfTokenValid('delete'.$borrowing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($borrowing);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_borrowing_index');
    }
}
