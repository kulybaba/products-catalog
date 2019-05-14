<?php

namespace App\Controller;

use App\Entity\Product;
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
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("")
     */
    public function list(Request $request, PaginatorInterface $paginator)
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->getAll();

        return $this->render('product/list.html.twig', [
            'products' => $paginator->paginate(
                $products,
                $request->query->getInt('page', 1),
                $this->getParameter('page_range')
            ),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/new")
     */
    public function new(Request $request)
    {
        $product = new Product();
        $product->setManager($this->getUser());

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('notice', 'Product created!');

            return $this->redirectToRoute('app_product_list');
        }

        return $this->render('product/newEdit.html.twig', [
            'form' => $form->createView()
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

            return $this->redirectToRoute('app_product_list');
        }

        return $this->render('product/newEdit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
