<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Entity\Product;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/products")
 */
class ProductController extends AbstractController
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

        $products = $this->getDoctrine()->getRepository(Product::class)->getAll(['manager' => $this->getUser()]);

        return $this->json([
            'products' => $paginator->paginate(
                $products,
                $request->query->getInt('page', 1),
                $this->getParameter('page_range')
            ),
        ]);
    }

    /**
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/category/{id}", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function listByCategory(Request $request, PaginatorInterface $paginator, Category $category)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $products = $this->getDoctrine()->getRepository(Product::class)->getAll(['categoryId' => $category->getId()]);

        return $this->json([
            'products' => $paginator->paginate(
                $products,
                $request->query->getInt('page', 1),
                $this->getParameter('page_range')
            ),
        ]);
    }

    /**
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/new", methods={"POST"})
     */
    public function new(Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$request->getContent()) {
            throw new HttpException('400', 'Bad request');
        }

        /** @var Product $product */
        $product = $serializer->deserialize($request->getContent(), Product::class, JsonEncoder::FORMAT);
        $product->setManager($this->getUser());

        if (count($validator->validate($product, null, 'apiNew'))) {
            throw new HttpException('400', 'Bad request');
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->json(['product' => $product]);
    }
}
