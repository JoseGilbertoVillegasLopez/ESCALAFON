<?php

namespace App\Form;

use App\Entity\Categoria;
use App\Entity\InformacionLaboral;
use App\Entity\InformacionPersonal;
use App\Entity\Puesto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformacionLaboralType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numeroAfiliado')
            ->add('fechaIncorporacion')
            ->add('tipoPlaza')
            ->add('turnoactual')
            ->add('jornada')
            ->add('informacionPersonal', EntityType::class, [
                'class' => InformacionPersonal::class,
                'choice_label' => 'id',
            ])
            ->add('puesto', EntityType::class, [
                'class' => Puesto::class,
                'choice_label' => 'id',
            ])
            ->add('categoria', EntityType::class, [
                'class' => Categoria::class,
                'choice_label' => 'id',
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
