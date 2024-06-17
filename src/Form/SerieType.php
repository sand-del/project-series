<?php

namespace App\Form;

use App\Entity\Serie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Title'
            ])
            ->add('overview', TextareaType::class, ['required' => false])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Canceled' => 'canceled',
                    'Ended' => 'ended',
                    'Returning' => 'returning'
                ],
                'attr' => [
                    'class' => 'testClass'
                ]
            ])
            ->add('vote')
            ->add('popularity')
            ->add('genres', ChoiceType::class, [
                'choices' => [
                    'Western' => 'western',
                    'Comedie' => 'comedie',
                    'Romance' => 'romance',
                    'Fantasy' => 'fantasy',
                    'SF' => 'sf',
                    'Action' => 'action'
                ],
                'expanded' => true //permet de transformer en radio bouton
            ])
            ->add('firstAirDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('lastAirDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('backdrop')
            ->add('poster', FileType::class, [
                //on demande de ne pas mapper le form avec l'entité car sur l'entité c'est un string et ici on cherche à enregistrer un fichier
                'mapped' => false,
                'constraints' => [
                    new Image(
                        [
                            'maxSize' => '10m',
                            'mimeTypesMessage' => 'Image format not allowed',
                            'maxSizeMessage' => 'The file is too large'
                        ]
                    )
                ]
            ])
            ->add('tmdbId');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class,
            'required' => false
        ]);
    }
}
