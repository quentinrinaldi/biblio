<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class AccountFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label_attr' => ['col-3 col-form-label'],
                'disabled' => true,
                'attr' => ['class' => 'form-control col-5 offset-2'],
                'row_attr' => [
                    'class' => 'form-group row'
            ]])
            ->add('firstName', TextType::class, [
                'label_attr' => ['col-3 col-form-label'],
                'attr' => ['class' => 'form-control col-5 offset-2'],
                'row_attr' => [
                    'class' => 'form-group row']
                ])
            ->add('lastName', TextType::class, [
                'label_attr' => ['col-3 col-form-label'],
                'attr' => ['class' => 'form-control col-5 offset-2'],
                'row_attr' => [
                    'class' => 'form-group row']
            ])
            ->add('email', EmailType::class, [
                'label_attr' => ['col-3 col-form-label'],
                'label' => 'Email Add.',
                'attr' => ['class' => 'form-control col-5 offset-2'],
                'row_attr' => [
                    'class' => 'form-group row']
            ])
            ->add('address', TextType::class, [
                'label_attr' => ['col-3 col-form-label'],
                'attr' => ['class' => 'form-control col-5 offset-2'],
                'row_attr' => [
                    'class' => 'form-group row']
            ])
            ->add('phoneNumber', TextType::class, [
                'label_attr' => ['col-3 col-form-label'],
                'attr' => ['class' => 'form-control col-5 offset-2'],
                'row_attr' => [
                    'class' => 'form-group row']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
