<?php

namespace App\Helper;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;

trait FormTrait
{
    /**
     * @param $data
     *
     * @return FormInterface
     */
    private function createSearchForm($data)
    {
        return $this->createFormBuilder(null, ['csrf_protection' => false])
            ->add('keyword', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Product name',
                    'class' => 'form-control',
                    ],
                'data' => $data,
            ])
            ->getForm();
    }
}
