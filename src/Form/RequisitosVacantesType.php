<?php

namespace App\Form;

use App\Entity\Cursos;
use App\Entity\RequisitosVacantes;
use App\Entity\Vacantes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequisitosVacantesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('curso', EntityType::class, [
                'class' => Cursos::class,
                'choice_label' => 'nombre',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RequisitosVacantes::class,
        ]);
    }
}
