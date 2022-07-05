<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class AddEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $array_options_gyms = [];
        foreach ($options['data']['gyms'] as $key => $value){
            $array_options_gyms[$value->getName()] = "".$value->getId();
        }

        $builder
            ->add('title', ChoiceType::class, [
                'label' => 'Quel type d\'événement',
                'choices' => [
                    "Compétition" => "competition",
                    "Entraînement" => "entrainement",
                    "Renforcement" => "renforcement",
                    "Yoga" => "yoga",
                ],
            ])
            ->add('description')
            ->add('eventDate', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'placeholder' => [
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                    'hour' => 'Hour', 'minute' => 'Minute',
                ],
                'attr' => [
                    'min' => ( new \DateTime() )->format('Y-m-d H:i')
                ]
            ])
            ->add('endDate', DateTimeType::class, [
                'widget' => 'single_text',
                'placeholder' => [
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                    'hour' => 'Hour', 'minute' => 'Minute',
                ],
                'attr' => [
                    'min' => ( new \DateTime() )->format('Y-m-d H:i')
                ]
            ])
            ->add('gymId', ChoiceType::class, [
                'label' => 'Dans quelle salle',
                'choices' => $array_options_gyms,
            ])  
            ->add('save', SubmitType::class, [
                'label' => 'Créer l\'événement',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
        ;
    }
}