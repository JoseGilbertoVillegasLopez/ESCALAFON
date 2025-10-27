<?php

namespace App\Form;

use App\Entity\InformacionPersonal;
use App\Entity\InformacionLaboral;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('estadoCivil', ChoiceType::class, [
                'label' => 'Estado Civil',
                'required' => true,
                'choices' => [
                    'Soltero(a)' => 'false',
                    'Casado(a)' => 'true',
                ],
                'expanded' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Seleccione el estado civil',
                    'maxlength' => 15,
                    'data-toggle' => 'tooltip',
                    'title' => 'Seleccione el estado civil',
                ],
            ])
            ->add('domicilio')
            ->add('imagen')

            //iformationLaboral
            ->add('informacionLaboral', InformacionLaboralType::class,[
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InformacionPersonal::class,
        ]);
    }
}
