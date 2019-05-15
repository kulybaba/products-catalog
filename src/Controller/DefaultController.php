<?php

namespace App\Controller;

use App\Entity\Product;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/")
     */
    public function index(Request $request, PaginatorInterface $paginator)
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
}
