<?php

namespace App\Normalizer;

use App\Entity\Product;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProductNormalizer implements NormalizerInterface
{
    public function normalize($product, $format = null, array $context = []): array
    {
        return $data = [
            'id' => $product->getId(),
            'managerId' => $product->getManager()->getId(),
            'image' => $product->getImage() ? $product->getImage()->getUrl() : 'images/product/noimage.jpg',
            'description' => $product->getDescription(),
            'color' => $product->getColor(),
            'count' => $product->getCount(),
            'price' => $product->getPrice(),
            'currency' => $product->getCurrency(),
            'createdAt' => $product->getCreatedAt(),
            'updatedAt' => $product->getUpdatedAt(),
        ];
    }

    public function supportsNormalization($product, $format = null): bool
    {
        return $product instanceof Product;
    }
}
