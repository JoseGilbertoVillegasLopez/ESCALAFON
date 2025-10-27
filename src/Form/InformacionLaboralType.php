<?php

namespace App\Form;

use App\Entity\Categoria;
use App\Entity\InformacionLaboral;
use App\Entity\InformacionPersonal;
use App\Entity\Puesto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformacionLaboralType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numeroAfiliado')
            ->add('fechaIncorporacion', DateType::class, [

                'widget' => 'single_text', // esto renderiza un campo de entrada de texto simple
                'label' => 'Fecha de Incorporación', // esto establece la etiqueta del campo
                'format' => 'yyyy-MM-dd', //esto define el formato de la fecha
            ])
            ->add('tipoPlaza', ChoiceType::class, [
                'label' => 'Tipo de Plaza',
                'required' => true,
                'choices' => [
                    'Base' => 'true',
                    'Temporal' => 'false',
                ],
                'expanded' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione el tipo de plaza',
                    'maxlength' => 10,
                    'nimlength' => 9,
                    'data-toggle' => 'tooltip',
                    'title' => 'Seleccione el tipo de plaza laboral',
                ],
            ])
            ->add('turnoactual', ChoiceType::class, [
                'label' => 'Turno Actual',
                'required' => true,
                'choices' => [
                    'Matutino' => 'Matutino',
                    'Vespertino' => 'Vespertino',
                    'Nocturno' => 'Nocturno',
                    'Mixto' => 'Mixto',
                ],
                'expanded' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione el turno actual',
                    'maxlength' => 10,
                    'nimlength' => 9,
                    'data-toggle' => 'tooltip',
                    'title' => 'Seleccione el turno actual de trabajo',
                ],
            ])
            ->add('jornada', ChoiceType::class, [
                'label' => 'Jornada Laboral',
                'required' => true,
                'choices' => [
                    'Completa' => 'Completa',
                    'Parcial' => 'Parcial',
                ],
                'expanded' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione el tipo de jornada',
                    'maxlength' => 8,
                    'nimlength' => 7,
                    'data-toggle' => 'tooltip',
                    'title' => 'Seleccione el tipo de jornada laboral',
                ],
            ])

            ->add('puesto', EntityType::class, [
                'class' => Puesto::class,
                'choice_label' => 'nombre',
            ])
            ->add('categoria', EntityType::class, [
                'class' => Categoria::class,
                'choice_label' => 'nombre',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InformacionLaboral::class,
        ]);
    }
}
