<?php

namespace App\Form;

use App\Entity\Categoria;
use App\Entity\Puesto;
use App\Entity\Vacantes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VacantesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('antiguedad')
            ->add('puesto', EntityType::class, [
                'class' => Puesto::class,
                'choice_label' => 'nombre',
            ])
            ->add('categoria', EntityType::class, [
                'class' => Categoria::class,
                'choice_label' => 'nombre',
            ])
            ->add('requisitos', CollectionType::class, [
                'entry_type' => RequisitosVacantesType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'attr' => ['class' => 'collection-requisitos'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vacantes::class,
        ]);
    }
}
