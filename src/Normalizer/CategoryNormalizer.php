<?php

namespace App\Normalizer;

use App\Entity\Category;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CategoryNormalizer implements NormalizerInterface
{
    public function normalize($category, $format = null, array $context = []): array
    {
        return $data = [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'position' => $category->getPosition(),
        ];
    }

    public function supportsNormalization($category, $format = null): bool
    {
        return $category instanceof Category;
    }
}
