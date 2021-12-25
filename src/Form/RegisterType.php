<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class RegisterType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'label_attr' => ['class' => 'form-label poppins light'],
                'attr' => [
                    'class' => 'form-control poppins',
                    'placeholder' => 'John',
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'label_attr' => ['class' => 'form-label poppins light'],
                'attr' => [
                    'class' => 'form-control poppins',
                    'placeholder' => 'Doe',
                ],
            ])
            ->add('birthdate', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'label_attr' => ['class' => 'form-label poppins light'],
                'attr' => ['class' => 'form-control poppins'],
            ])
            ->add('username', TextType::class, [
                'label' => 'Pseudo',
                'label_attr' => ['class' => 'form-label poppins light'],
                'attr' => [
                    'class' => 'form-control poppins',
                    'placeholder' => 'johndoe123',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'label_attr' => ['class' => 'form-label poppins light'],
                'attr' => [
                    'class' => 'form-control poppins',
                    'placeholder' => 'jdoe@gmail.com',
                ],
            ])
            ->add('password', RepeatedType::class, [
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Répéter mot de passe'],
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => [
                    'label_attr' => ['class' => 'form-label poppins light'],
                    'attr' => [
                        'class' => 'form-control poppins',
                        'placeholder' => '••••••••••',
                    ],
                ],
                'required' => true,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}