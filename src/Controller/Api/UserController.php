<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\UserService;
use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class UserController
 * @package App\Controller\Api
 * @Route("/api")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/registration", methods={"POST"})
     */
    public function registaration(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserService $userService,
        EmailService $emailService
    ) {
        if (!$request->getContent()) {
            throw new HttpException('400', 'Bad request');
        }

        /** @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, JsonEncoder::FORMAT);
        $user->setRoles([User::ROLE_ADMIN_MANAGER]);
        $user->setApiToken($userService->generateApiToken());

        if (count($validator->validate($user))) {
            throw new HttpException('400', 'Bad request');
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $emailService->sendRegistrationEmail($user);

        return $this->json(['user' => $user]);
    }
}
