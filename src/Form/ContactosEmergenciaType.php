<?php

namespace App\Form;

use App\Entity\ContactosEmergencia;
use App\Entity\InformacionPersonal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactosEmergenciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('parentesco')
            ->add('telefono')
            ->add('correo')
            ->add('informacionPersonal', EntityType::class, [
                'class' => InformacionPersonal::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactosEmergencia::class,
        ]);
    }
}
