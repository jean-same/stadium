<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => "nom de l'evenement",
                    'class' => "form-control"
                ]
            ])
            ->add('place', TextType::class, [
                'required' => false,
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => "adresse de l'evenement",
                    'class' => "form-control"
                ]
            ])
            ->add('picture', FileType::class, [
                'mapped' => false,
                'label' => "Image de l'evenement",
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid picture',
                    ])
                ],
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime',
                'required' => false,
                'label' => 'Date de debut',
                'attr' => [
                    'placeholder' => "date de debut de l'evenement",
                    'class' => "form-control"
                ]
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime',
                'required' => false,
                'label' => 'Date de fin',
                'attr' => [
                    'placeholder' => "date de fin de l'evenement",
                    'class' => "event-endDate-field form-control"
                ]
            ])
            ->add('schedule', TimeType::class, [
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
