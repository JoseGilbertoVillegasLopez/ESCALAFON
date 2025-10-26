<?php

namespace App\Form;

use App\Entity\InformacionPersonal;
use App\Entity\InformacionLaboral;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformacionPersonalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('apellidoPaterno')
            ->add('apellidoMaterno')
            ->add('telefonoFijo')
            ->add('telefonoCelular')
            ->add('correo')
            ->add('curp')
            ->add('rfc')
            ->add('nss')
            ->add('estadoCivil')
            ->add('domicilio')
            ->add('imagen')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InformacionPersonal::class,
        ]);
    }
}
