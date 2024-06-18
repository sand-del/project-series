<?php

namespace App\Form;

use App\Entity\Season;
use App\Entity\Serie;
use App\Repository\SerieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number')
            ->add('firstAirDate', null, [
                'widget' => 'single_text',
            ])
            ->add('overview')
            ->add('poster')
            ->add('tmdbId')
            ->add('serie', EntityType::class, [
                'class' => Serie::class,
                'choice_label' => 'name',
                //fonction anonyme qui permet d'ordonner notre select de serie par ordre alphabétique
                'query_builder' => function (SerieRepository $serieRepository) {
                    return $serieRepository->createQueryBuilder('s')->orderBy('s.name', 'ASC');
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
            'required' => false
        ]);
    }
}
