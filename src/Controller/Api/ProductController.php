<?php

namespace App\Controller\Api;

use App\Aws\S3Manager;
use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Product;
use App\Entity\Tag;
use App\Service\ImageService;
use Imagick;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Bad request');
        }

        /** @var Product $product */
        $product = $serializer->deserialize($request->getContent(), Product::class, JsonEncoder::FORMAT);
        $product->setManager($this->getUser());

        if (count($validator->validate($product, null, 'apiNew'))) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Bad request');
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->json(['product' => $product]);
    }

    /**
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{id}/edit", requirements={"id"="\d+"}, methods={"PUT"})
     */
    public function edit(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, Product $product)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$request->getContent()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Bad request');
        }

        $serializer->deserialize($request->getContent(), Product::class, JsonEncoder::FORMAT, [AbstractNormalizer::OBJECT_TO_POPULATE => $product]);

        if (count($validator->validate($product))) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Bad request');
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->json(['product' => $product]);
    }

    /**
     * @param Product $product
     * @param Category $category
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{product}/add-category/{category}", requirements={"product"="\d+", "category"="\d+"}, methods={"POST"})
     */
    public function addCategory(Product $product, Category $category)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $product->addCategory($category);
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->json([
            'code' => 200,
            'success' => true,
            'message' => 'Category added',
        ]);
    }

    /**
     * @param Product $product
     * @param Category $category
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{product}/remove-category/{category}", requirements={"product"="\d+", "category"="\d+"}, methods={"DELETE"})
     */
    public function removeCategory(Product $product, Category $category)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $product->removeCategory($category);
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->json([
            'code' => 200,
            'success' => true,
            'message' => 'Category removed',
        ]);
    }

    /**
     * @param Product $product
     * @param Tag $tag
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{product}/add-tag/{tag}", requirements={"product"="\d+", "tag"="\d+"}, methods={"POST"})
     */
    public function addTag(Product $product, Tag $tag)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $product->addTag($tag);
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->json([
            'code' => 200,
            'success' => true,
            'message' => 'Tag added',
        ]);
    }

    /**
     * @param Product $product
     * @param Tag $tag
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{product}/remove-tag/{tag}", requirements={"product"="\d+", "tag"="\d+"}, methods={"DELETE"})
     */
    public function removeTag(Product $product, Tag $tag)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $product->removeTag($tag);
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->json([
            'code' => 200,
            'success' => true,
            'message' => 'Tag removed',
        ]);
    }

    /**
     * @param Request $request
     * @param S3Manager $s3Manager
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \ImagickException
     *
     * @Route("/{id}/add-image", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function addImage(Request $request, S3Manager $s3Manager, Product $product, ImageService $imageService)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $content = $request->getContent();

        if (!$content) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Bad request');
        }

        $picture = new Imagick();

        if (!$picture->readImageBlob($content)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Bad request');
        }

        $extension = $imageService->getImageExtensionFromBinary($content);

        $result = $s3Manager->uploadPicture($content, $extension);

        $image = new Image();
        $image->setUrl($result['url']);
        $image->setS3Key($result['key']);

        $product->setImage($image);
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->json([
            'code' => 200,
            'success' => true,
            'message' => 'Image added',
        ]);
    }
}
