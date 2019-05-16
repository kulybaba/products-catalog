<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Helper\FormTrait;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    use FormTrait;

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param Category $category
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/category/{id}/products")
     */
    public function categoryProducts(Request $request, RequestStack $requestStack, PaginatorInterface $paginator, Category $category)
    {
        $params = [];
        $session = $request->getSession();
        $searchForm = $this->createSearchForm($session->get('productName'));

        $searchForm->handleRequest($requestStack->getMasterRequest());

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $session->set('productName', $searchForm->get('keyword')->getData());
        }

        if ($session->get('productName')) {
            $params['keyword'] = $session->get('productName');
        }

        $params['categoryId'] = $category->getId();

        $products = $this->getDoctrine()->getRepository(Product::class)->getAll($params);

        return $this->render('default/productsList.html.twig', [
            'products' => $paginator->paginate(
                $products,
                $request->query->getInt('page', 1),
                $this->getParameter('page_range')
            ),
            'form' => $searchForm->createView(),
        ]);
    }
}
