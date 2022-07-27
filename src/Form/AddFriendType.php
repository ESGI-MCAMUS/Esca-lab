<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AddFriendType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('username',   TextType::class, [
                'label' => 'Nom de l\'utilisateur',
                'label_attr' => ['class' => 'form-label poppins light'],
                'attr'  => [
                    'placeholder' => 'Trouvez l\'utilisateur',
                    'class' => 'form-control poppins',
                ]
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Chercher l\'utilisateur',
                'attr' => [
                    'class' => 'btn btn-outline-primary poppins',
                ],
            ]);
    }
}