<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AddFriendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username',   TextType::class, [
                'label' => 'Nom de l\'utilisateur',
                'attr'  => [
                    'placeholder' => 'Milan le plus bo'
                ]
            ])
            ->add('send',       SubmitType::class)
        ;
    }
}