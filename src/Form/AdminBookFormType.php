<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdminBookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('publishedDate', DateType::class, array(
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('thumbnailUrl', TextType::class, array(
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('description', TextareaType::class, array(
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('stars', NumberType::class, array(
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('publisher', TextType::class, array(
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('availableSince', DateType::class, array(
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('isPinned', CheckboxType::class, array(
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => ''
                ]))
            ->add('availability', CheckboxType::class, array(
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('isbn', TextType::class, array(
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('pagesCount', NumberType::class, array(
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('categories', EntityType::class, array(
                'required' => false,
                'class' =>Category::class,
                'by_reference' => false,
                'choice_label' => 'name',
                 'multiple' => true,
                 'expanded' => true,
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
