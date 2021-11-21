<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name' , TextType::class, [
                'required' => false,
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => "nom de l'evenement",
                    'class' => "event-name-field form-control"
                ]
            ])
            ->add('place', TextType::class, [
                'required' => false,
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => "adresse de l'evenement",
                    'class' => "event-address-field form-control"
                ]
            ])
            ->add('startDate' , DateType::class , [
                'widget' => 'single_text',
                'input'  => 'datetime',
                'required' => false,
                'label' => 'Date de debut',
                'attr' => [
                    'placeholder' => "date de debut de l'evenement",
                    'class' => "event-startDate-field form-control"
                ]
            ])
            ->add('endDate', DateType::class , [
                'widget' => 'single_text',
                'input'  => 'datetime',
                'required' => false,
                'label' => 'Date de fin',
                'attr' => [
                    'placeholder' => "date de fin de l'evenement",
                    'class' => "event-endDate-field form-control"
                ]
            ])
            ->add('schedule', TimeType::class , [
                'widget' => 'single_text',
                'input'  => 'datetime',
                'required' => false,
                'label' => 'Horaire',
                'attr' => [
                    'placeholder' => "Horaire l'evenement",
                    'class' => "event-schedule-field form-control"
                ]
            ])
            ->add('maxParticipants', IntegerType::class, [
                'required' => false,
                'label' => 'Max participants',
                'attr' => [
                    'placeholder' => "max participants",
                    'class' => "event-maxParticipants-field form-control"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
