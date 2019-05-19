<?php

namespace App\Controller\Api;

use App\Entity\Category;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/categories")
 */
class CategoryController extends AbstractController
{
    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("", methods={"GET"})
     */
    public function list(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $categories = $this->getDoctrine()->getRepository(Category::class)->getAll(['managerId' => $this->getUser()->getId()]);

        return $this->json([
            'categories' => $paginator->paginate(
                $categories,
                $request->query->getInt('page', 1),
                $this->getParameter('page_range')
            ),
        ]);
    }
}
