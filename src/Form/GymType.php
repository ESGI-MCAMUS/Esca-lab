<?php

namespace App\Form;

use App\Entity\Gym;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class GymType extends AbstractType {
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la salle',
                'label_attr' => ['class' => 'form-label poppins light'],
                'attr' => [
                    'class' => 'form-control poppins',
                    'placeholder' => 'Ma Super Salle',
                    'required' => true,
                ],
            ])
            ->add('size', NumberType::class, [
                'label' => 'Taille de la salle (en m²)',
                'label_attr' => ['class' => 'form-label poppins light'],
                'attr' => [
                    'class' => 'form-control poppins',
                    'placeholder' => 150,
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse postale de la salle',
                'label_attr' => ['class' => 'form-label poppins light'],
                'attr' => [
                    'class' => 'form-control poppins',
                    'placeholder' => '38 rue des Jeuneurs',
                ],
            ])
            ->add('pc', TextType::class, [
                'label' => 'Code postal de la salle',
                'label_attr' => ['class' => 'form-label poppins light'],
                'attr' => [
                    'class' => 'form-control poppins',
                    'placeholder' => 75012,
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville de la salle',
                'label_attr' => ['class' => 'form-label poppins light'],
                'attr' => [
                    'class' => 'form-control poppins',
                    'placeholder' => 'Paris',
                ],
            ])
            ->add('picture', FileType::class, [
                'mapped' => false,
                'label_attr' => ['class' => 'form-label'],
                'label' => "Photo de la salle",
                'attr' => [
                    'class' => 'form-control poppins',
                ],
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Ce fichier n\'est pas une image',
                    ])
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Gym::class,
        ]);
    }
}