<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Star;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StarController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/product/star")
     */
    public function star()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $product = $this->getDoctrine()->getRepository(Product::class)->find(Request::createFromGlobals()->request->get('id'));
        $user = $this->getUser();

        if (!$this->getDoctrine()->getRepository(Star::class)->findOneBy(['product' => $product->getId(), 'user' => $user->getId()])) {
            $star = new Star();
            $star->setProduct($product);
            $star->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($star);
            $em->flush();

            return $this->json([
                'success' => true,
                'starsCount' => $product->getStars()->count(),
            ]);
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/product/unstar")
     */
    public function unstar()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $id = Request::createFromGlobals()->request->get('id');

        if ($star = $this->getDoctrine()->getRepository(Star::class)->findOneBy(['user' => $this->getUser()->getId(), 'product' => $id])) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($star);
            $em->flush();

            return $this->json([
                'success' => true,
                'starsCount' => $this->getDoctrine()->getRepository(Product::class)->find($id)->getStars()->count(),
            ]);
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/stars")
     */
    public function starsList()
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('star/list.html.twig', [
            'stars' => $user->getStars(),
        ]);
    }
}
