<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
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
        $params = [];
        /** @var User $user */
        $user = $this->getUser();
        if ($user && $user->getRoles() == ['ROLE_ADMIN_MANAGER']) {
            $params['managerId'] = $user->getId();
        }

        $products = $this->getDoctrine()->getRepository(Product::class)->getAll($params);

        return $this->render('product/list.html.twig', [
            'products' => $paginator->paginate(
                $products,
                $request->query->getInt('page', 1),
                $this->getParameter('page_range')
            ),
        ]);
    }

    public function lastProducts()
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user && $user->getRoles() == ['ROLE_ADMIN_MANAGER']) {
            $products = $user->getProducts();
        }
        $products = $this->getDoctrine()->getRepository(Product::class)->findLastProducts($this->getParameter('last_products'));

        return $this->render('default/lastProducts.html.twig', [
            'products' => $products,
        ]);
    }

    public function categoriesList()
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user && $user->getRoles() == ['ROLE_ADMIN_MANAGER']) {
            $categories = $user->getCategory();
        }
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('default/categoriesList.html.twig', [
            'categories' => $categories,
        ]);
    }
}
