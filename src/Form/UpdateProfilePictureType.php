<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class UpdateProfilePictureType extends AbstractType {
  public function buildForm(FormBuilderInterface $builder, array $options): void {
    $builder->add('profile_picture', FileType::class, [
      'mapped' => false,
      'label_attr' => ['class' => 'form-label'],
      'label' => "",
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
    ]);
  }

  public function configureOptions(OptionsResolver $resolver): void {
    $resolver->setDefaults([
      'data_class' => User::class,
    ]);
  }
}