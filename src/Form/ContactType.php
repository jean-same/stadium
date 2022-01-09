<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('role', ChoiceType::class, [
                'label' => "Vous etes",
                'choices' => [
                    '-choisir-' => "",
                    'Un utilisateur' => "user",
                    'Une association' => "assoc"
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas etre vide',
                    ])
                ],
            ])
            ->add('reason', ChoiceType::class, [
                'label' => "Motif",
                'choices' => [
                    '-choisir-' => "",
                    'Inscription' => "Inscription",
                    'Connexion' => "Connexion",
                    'Autres' => "Autres"
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut pas etre vide',
                    ])
                ],
            ])
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => "Votre adresse email",
                    'attr' => [
                        'placeholder' => "contact@test.fr"
                    ],
                    'constraints' => [
                        new Email([
                            'message' => 'Email invalide',
                        ])
                    ],
                ]
            )
            ->add('description', TextareaType::class, [
                'label' => "Votre message",
                'required' => false,
                'attr' => [
                    'rows' => '8'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous ne pouvez pas envoyer un message vide',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le message est trop court, il vous faut au moins 200 caracteres',
                        'max' => 1000,
                        'maxMessage' => 'Le message est trop long, max 1000 caracteres'
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
