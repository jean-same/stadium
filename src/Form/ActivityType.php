<?php

namespace App\Form;

use App\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => "nom de l'activité",
                    'class' => "activity-name-field form-control"
                ]
            ])
            /* ->add('picture' , TextType::class, [
                'required' => false,
                'label' => 'Image',
                'attr' => [
                    'placeholder' => "Image de l'activité",
                    'class' => 'activity-picture-field form-control'
                ]
            ]) */
            ->add('picture', FileType::class, [
                'mapped' => false,
                'label' => "Image de l'activité",
                'required' => false,
                'attr' => [
                    'placeholder' => "Image de l'activité",
                    'class' => 'activity-picture-field form-control'
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
