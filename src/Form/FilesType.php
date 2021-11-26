<?php

namespace App\Form;

use App\Entity\File as FileEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File as FileConstraints;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class FilesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phoneNumber', IntegerType::class, [
                'required' => false,
                'label' => 'Numéro de téléphone',
                'attr' => [
                    'placeholder' => "Ton numero de téléphone",
                    'class' => "form-control"
                ]
            ])
            ->add('dateOfBirth', DateType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime',
                'required' => false,
                'label' => 'Date de naissance',
                'attr' => [
                    'placeholder' => "Ta date de naissance",
                    'class' => "form-control"
                ]
            ])
            ->add('address', TextType::class, [
                'required' => false,
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => "Ton adresse",
                    'class' => "form-control"
                ]
            ])
            ->add('emergencyContactName', TextType::class, [
                'required' => false,
                'label' => 'Emergency Contact Name',
                'attr' => [
                    'placeholder' => "Le nom de la personne à contacter en cas d'urgence",
                    'class' => "form-control"
                ]
            ])
            ->add('emergencyContactPhoneNumber', IntegerType::class, [
                'required' => false,
                'label' => 'Emergency Contact Number',
                'attr' => [
                    'placeholder' => "Le numméro de la personne à contacter en cas d'urgence",
                    'class' => "form-control"
                ]
            ])
            ->add('medicalCertificate', FileType::class, [
                'label' => "Certificat medical(pdf)",
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new FileConstraints([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'application/pdf'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid pdf',
                    ])
                ],
            ])
            ->add('rulesOfProcedure', FileType::class, [
                'label' => "Reglements interieur(pdf)",
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new FileConstraints([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'application/pdf'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid pdf',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FileEntity::class,
        ]);
    }
}
