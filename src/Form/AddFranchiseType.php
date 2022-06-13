<?php

namespace App\Form;

use App\Entity\Franchise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AddFranchiseType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $usersSelect = [];
    foreach ($options['users'] as $user => $value) {
      $usersSelect[
        $value->getFirstname() .
          ' ' .
          $value->getLastname() .
          ' (' .
          $value->getEmail() .
          ')'
      ] = $value->getId();
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
        ],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Franchise::class,
    ]);
    $resolver->setRequired('users');
  }
}