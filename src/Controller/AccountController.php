<?php

namespace App\Controller;

use App\Entity\Borrowing;
use App\Form\AccountFormType;
use App\Repository\BorrowingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
    /**
     * @Route("/account", name="account_")
     */
class AccountController extends AbstractController
{
    /**
     * @Route("/edit", name="edit")
     */
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(AccountFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Your changes have been saved');
            return $this->redirectToRoute('account_edit');
        }

        return $this->render('frontEnd/account/edit.html.twig', [
            '$user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/borrowing", name="borrowing")
     */
    public function index(BorrowingRepository $borrowingRepository): Response
    {
        $user = $this->getUser();
        $borrowings = $user->getCurrentBorrowings();

        return $this->render('frontEnd/account/index_borrowings.html.twig', [
            'borrowings' => $borrowings
        ]);
    }
}
