<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminPanelController extends AbstractController
{
    /**
     * @Route("")
     */
    public function index()
    {
        return $this->render('admin_panel/index.html.twig', [
            'usersCount' => count($this->getDoctrine()->getRepository(User::class)->findAll()),
            'productsCount' => count($this->getDoctrine()->getRepository(Product::class)->findAll()),
            'categoriesCount' => count($this->getDoctrine()->getRepository(Category::class)->findAll()),
            'lastUsers' => $this->getDoctrine()->getRepository(User::class)->findLast($this->getParameter('last_admin')),
            'lastCategories' => $this->getDoctrine()->getRepository(Category::class)->findLast($this->getParameter('last_admin')),
            'lastProducts' => $this->getDoctrine()->getRepository(Product::class)->findLast($this->getParameter('last_admin')),
        ]);
    }
}
