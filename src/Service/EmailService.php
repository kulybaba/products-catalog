<?php

namespace App\Service;

use App\Entity\User;

class EmailService
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
            ->setFrom('ahurtep@gmai.com')
            ->setFrom($this->adminEmail)
            ->setTo($user->getEmail())
            ->setBody('Congratulations! ' . $user->getFirstName() . ' ' . $user->getLastName() . ', you are successfully registered. ' . 'Email: ' . $user->getEmail() . '. Password: ' . $user->getPlainPassword() . '.');
        $this->mailer->send($message);
    }
}
