<?php

namespace App\Controller;

use App\Aws\S3Manager;
use App\Entity\Comment;
use App\Entity\Product;
use App\Form\CommentType;
use App\Form\ProductType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/products")
 */
class ProductController extends AbstractController
{
    /**
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/{id}/view")
     */
    public function view(Request $request, PaginatorInterface $paginator, Product $product)
    {
        $comment = new Comment();
        $comment->setUser($this->getUser());
        $comment->setProduct($product);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('notice', 'Comment created!');

            return $this->redirectToRoute('app_product_view', [
                'id' => $product->getId(),
            ]);
        }

        $comments = $this->getDoctrine()->getRepository(Comment::class)->getAll(['product' => $product]);

        return $this->render('product/view.html.twig', [
            'product' => $product,
            'comments' => $paginator->paginate(
                $comments,
                $request->query->getInt('page', 1),
                $this->getParameter('page_range')
            ),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param S3Manager $s3Manager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/new")
     */
    public function new(Request $request, S3Manager $s3Manager)
    {
        $product = new Product();
        $product->setManager($this->getUser());

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($image = $product->getImage()) {
                $imageFile = $this->file($image->getUrl())->getFile();
                $result = $s3Manager->uploadPicture($imageFile);
                $product->getImage()->setUrl($result['url']);
                $product->getImage()->setS3Key($result['key']);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('notice', 'Product created!');

            return $this->redirectToRoute('app_default_index');
        }

        return $this->render('product/newEdit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Create',
        ]);
    }

    /**
     * @param Request $request
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/{id}/edit")
     */
    public function edit(Request $request, Product $product)
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('notice', 'Product updated!');

            return $this->redirectToRoute('app_default_index');
        }

        return $this->render('product/newEdit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Edit',
        ]);
    }

    /**
     * @param Product $product
     * @param S3Manager $s3Manager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Exception
     *
     * @Route("/{id}/delete")
     */
    public function delete(Product $product, S3Manager $s3Manager)
    {
        try {
            if ($product->getImage()) {
                $s3Manager->deletePicture($product->getImage()->getS3Key());
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();

            $this->addFlash('notice', 'Product deleted!');

            return $this->redirectToRoute('app_default_index');
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
