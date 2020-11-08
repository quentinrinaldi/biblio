<?php

namespace App\Form;

use App\Entity\Artist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminArtistFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, array(
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('firstName', TextType::class, array(
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('presentation', TextareaType::class, array(
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('birthDate', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('deathDate', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
                ->add('update', SubmitType::class, array(
                    'attr' => ['class' => 'btn btn-primary']))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Artist::class,
        ]);
    }
}
