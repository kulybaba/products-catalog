<?php

namespace App\Normalizer;

use App\Entity\Product;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProductNormalizer implements NormalizerInterface
{
    public function normalize($product, $format = null, array $context = []): array
    {
        $categories = [];
        $tags = [];
        foreach ($product->getCategory() as $category) {
            $categories[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'position' => $category->getPosition(),
            ];
        }
        foreach ($product->getTag() as $tag) {
            $tags[] = [
                'id' => $tag->getId(),
                'text' => $tag->getText(),
                'position' => $tag->getPosition(),
            ];
        }

        return $data = [
            'id' => $product->getId(),
            'managerId' => $product->getManager()->getId(),
            'image' => $product->getImage() ? $product->getImage()->getUrl() : 'images/product/noimage.jpg',
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'categories' => $categories,
            'tags' => $tags,
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
