<?php

namespace App\Form;

use App\Entity\Route;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RouteType extends AbstractType
{

    private $difficulty = ["1" => "1", "2" => "2", "3" => "3", "4" => "4", "5a" => "5a", "5b" => "5b", "5c" => "5c",
        "6a" => "6a", "6a+" => "6a+", "6b" => "6b", "6b+" => "6b+", "6c" => "6c", "6c+" => "6c+",
        "7a" => "7a", "7a+" => "7a+", "7b" => "7b", "7b+" => "7b+", "7c" => "7c", "7c+" => "7c+",
        "8a" => "8a", "8a+" => "8a+", "8b" => "8b", "8b+" => "8b+", "8c" => "8c", "8c+" => "8c+",
        "9a" => "9a", "9a+" => "9a+", "9b" => "9b", "9b+" => "9b+", "9c" => "9c"];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la route',
                'label_attr' => ['class' => 'form-label poppins light'],
                'attr' => [
                    'class' => 'form-control poppins',
                    'placeholder' => 'Ma Super Route',
                    'required' => true,
                ],
            ])
            ->add('difficulty', ChoiceType::class, [
                'label' => 'Difficulté',
                'choices' => $this->difficulty,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => "btn btn-primary"],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Route::class,
        ]);
    }
}
