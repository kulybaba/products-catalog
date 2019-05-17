<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmailService extends AbstractController
{
    private $mailer;

    private $adminEmail;

    public function __construct(string $adminEmail, \Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
        $this->adminEmail = $adminEmail;
    }

    public function sendRegistrationEmail(User $user)
    {
        $message = (new \Swift_Message('Registration email'))
            ->setFrom($this->adminEmail)
            ->setTo($user->getEmail())
            ->setBody('Congratulations! ' . $user->getFirstName() . ' ' . $user->getLastName() . ', you are successfully registered. ' . 'Email: ' . $user->getEmail() . '. Password: ' . $user->getPlainPassword() . '.');
        $this->mailer->send($message);
    }

    public function sendProductsEmail($products)
    {
        $message = (new \Swift_Message('New products'))
            ->setFrom($this->adminEmail)
            ->setTo($this->adminEmail)
            ->setBody(
                $this->renderView(
                    'emails/products.html.twig',
                    [
                        'products' => $products,
                    ]
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }
}
