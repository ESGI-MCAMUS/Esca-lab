<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nom de l\'employé',
                'label_attr' => ['class' => 'form-label poppins light'],
                'attr'  => [
                    'placeholder' => 'John Doe',
                    'class' => 'form-control poppins',

                ]
            ])
            ->add('send', SubmitType::class, ['attr' => ['class' => 'btn btn-primary'], 'label' => 'Rechercher',]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}