<?php

namespace App\Form;

use App\Entity\Categoria;
use App\Entity\Puesto;
use App\Entity\Vacantes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VacantesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('antiguedad', IntegerType::class, [
                'label' => 'Antigüedad requerida (años)',
                'attr' => ['min' => 0],
            ])
            ->add('puesto', EntityType::class, [
                'class' => Puesto::class,
                'choice_label' => 'nombre',
                'label' => 'Puesto asociado',
            ])
            ->add('categoria', EntityType::class, [
                'class' => Categoria::class,
                'choice_label' => 'nombre',
                'label' => 'Categoría',
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Describe brevemente las funciones del puesto...',
                ],
            ])
            ->add('numeroVacantes', IntegerType::class, [
                'label' => 'Número total de vacantes',
                'attr' => ['min' => 1],
            ])
            ->add('requisitos', CollectionType::class, [
                'entry_type' => RequisitosVacantesType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->add('activo', CheckboxType::class, [
                'required' => false,
                'label' => 'Vacante activa',
                'attr' => ['hidden' => 'true'],
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
