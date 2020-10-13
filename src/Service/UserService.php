<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var ValidatorInterface $validator
     */
    private $validator;

    /**
     * @var UserPasswordEncoderInterface $passwordEncoder
     */
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->validator = $validator;
    }

    /**
     * @return string
     */
    public function generateApiToken()
    {
        return Uuid::uuid4()->toString();
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return array
     */
    public function createSuperAdmin(string $email, string $password)
    {
        $user = new User();
        $user->setFirstName('Super');
        $user->setLastName('Admin');
        $user->setEmail($email);
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setPlainPassword($password);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
        $user->setApiToken($this->generateApiToken());

        $result = ['user' => $user, 'errors' => []];
        if (count($errors = $this->validator->validate($user, null, 'super_admin')) > 0) {
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $result['errors'][] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage(),
                    'invalidValue' => $error->getInvalidValue(),
                ];
            }
        } else {
            $this->em->persist($user);
            $this->em->flush();
        }

        return $result;
    }
}
