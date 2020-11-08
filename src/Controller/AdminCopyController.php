<?php

namespace App\Controller;

use App\Entity\Copy;
use App\Form\AdminCopyFormType;
use App\Repository\CopyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/copy", name="admin_copy_")
 */
class AdminCopyController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(CopyRepository $copyRepository): Response
    {
        return $this->render('backEnd/copy/index.html.twig', [
            'copies' => $copyRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $copy = new Copy();
        $form = $this->createForm(AdminCopyFormType::class, $copy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($copy);
            $entityManager->flush();

            return $this->redirectToRoute('admin_copy_index');
        }

        return $this->render('backEnd/copy/new.html.twig', [
            'copy' => $copy,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Copy $copy): Response
    {
        return $this->render('backEnd/copy/show.html.twig', [
            'copy' => $copy,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="copy_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Copy $copy): Response
    {
        $form = $this->createForm(AdminCopyFormType::class, $copy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_copy_index');
        }

        return $this->render('backEnd/copy/edit.html.twig', [
            'copy' => $copy,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Copy $copy): Response
    {
        if ($this->isCsrfTokenValid('delete'.$copy->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($copy);
            $entityManager->flush();
        }

        return $this->redirectToRoute('index');
    }
}
