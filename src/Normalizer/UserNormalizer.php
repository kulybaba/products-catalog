<?php

namespace App\Normalizer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface
{
    public function normalize($user, $format = null, array $context = []): array
    {
        return [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'apiToken' => $user->getApiToken(),
        ];
    }

    public function supportsNormalization($user, $format = null): bool
    {
        return $user instanceof User;
    }
}
