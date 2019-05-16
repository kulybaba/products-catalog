<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/comments")
 */
class CommentController extends AbstractController
{
    /**
     * @param Request $request
     * @param Comment $comment
     * @param int $productId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/{id}/edit")
     */
    public function edit(Request $request, Comment $comment)
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('notice', 'Comment updated!');

            return $this->redirectToRoute('app_product_view', [
                'id' => $comment->getProduct()->getId(),
            ]);
        }

        return $this->render('comment/edit.html.twig', [
            'form' => $form->createView(),
            'productId' => $comment->getProduct()->getId(),
        ]);
    }
}
