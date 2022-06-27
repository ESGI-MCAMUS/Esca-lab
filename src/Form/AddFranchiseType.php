<?php

namespace App\Form;

use App\Entity\Franchise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class AddFranchiseType extends AbstractType {
  public function buildForm(FormBuilderInterface $builder, array $options): void {
    $usersSelect = [];
    foreach ($options['users'] as $user => $value) {
      $usersSelect[$value->getFirstname() .
        ' ' .
        $value->getLastname() .
        ' (' .
        $value->getEmail() .
        ')'] = $value->getId();
    }
    dump($usersSelect);
    $builder
      ->add('admin', ChoiceType::class, [
        'choices' => $usersSelect,
        'label' => 'Administrateur de la franchise',
        'label_attr' => ['class' => 'form-label poppins light'],
        'attr' => [
          'class' => 'form-select poppins',
        ],
      ])
      ->add('name', TextType::class, [
        'label' => 'Nom de la franchise',
        'label_attr' => ['class' => 'form-label poppins light'],
        'attr' => [
          'class' => 'form-control poppins',
          'placeholder' => 'Franchise & Co.',
          'required' => true,
        ],
      ])->add('picture', FileType::class, [
        'mapped' => false,
        'label_attr' => ['class' => 'form-label poppins light'],
        'label' => "Image de la franchise",
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
      'data_class' => Franchise::class,
    ]);
    $resolver->setRequired('users');
  }
}