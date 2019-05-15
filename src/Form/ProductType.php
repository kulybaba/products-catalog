<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextType::class, [
                'required' => false,
            ])
            ->add('color', ColorType::class)
            ->add('count', IntegerType::class)
            ->add('price', NumberType::class)
            ->add('currency', TextType::class)
            ->add('image', ImageType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => [
                    'class' => 'multi-selector',
                ],
            ])
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'text',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Tags',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
