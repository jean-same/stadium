<?php

namespace App\Form;

use App\Entity\Association;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AssociationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => "Le nom complet de l'association"
                ]
            ])
            ->add('presidentLastName', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => "Le nom du président de l'association"
                ]
            ])
            ->add('presidentFirstName', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => "Le prénom du président de l'association"
                ]
            ])
            ->add('address', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => "L'adresse complete de l'association"
                ]
            ])
            ->add('phoneNumber', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => "Numéro de téléphone de l'association",
                ]
            ])
            ->add('image', FileType::class, [
                'label' => false,
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
            ->add('description', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => "Décrivez brièvement votre association"
                ]
            ])
            ->add('postCode' , HiddenType::class)
            ->add('lat' , HiddenType::class)
            ->add('lng' , HiddenType::class)
            ->add('city' , HiddenType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Association::class,
        ]);
    }
}
