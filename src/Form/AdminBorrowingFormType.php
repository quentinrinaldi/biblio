<?php

namespace App\Form;

use App\Entity\Borrowing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminBorrowingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('createdAt', DateType::class, array(
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('expectedReturnDate', DateType::class, array(
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('returnedAt', DateType::class, array(
                'attr' => ['class' => 'form-control'],
                'row_attr' => [
                    'class' => 'form-group'
                ]))
            ->add('status')
            ->add('user')
            ->add('copy')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Borrowing::class,
        ]);
    }
}
