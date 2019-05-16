<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param Category $category
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/category/{id}/products")
     */
    public function categoryProducts(Request $request, PaginatorInterface $paginator, Category $category)
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->getAll([
            'categoryId' => $category->getId()
        ]);

        return $this->render('default/productsList.html.twig', [
            'products' => $paginator->paginate(
                $products,
                $request->query->getInt('page', 1),
                $this->getParameter('page_range')
            ),
        ]);
    }
}
