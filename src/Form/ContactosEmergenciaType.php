<?php

namespace App\Form;

use App\Entity\ContactosEmergencia;
use App\Entity\InformacionPersonal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactosEmergenciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre' , TextType::class, [
                'label' => 'Nombre completo',
                'attr' => ['class' => 'form-control mb-2']
                ])
            ->add('parentesco', ChoiceType::class, [
                'label' => 'Parentesco',
                'required' => true,
                'choices' => [
                    'Padre/Madre' => 'Padre/Madre',
                    'Hermano(a)' => 'Hermano(a)',
                    'Esposo(a)' => 'Esposo(a)',
                    'Amigo(a)' => 'Amigo(a)',
                    'Otro' => 'Otro',
                ],
                'expanded' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-control mb-2',
                    'placeholder' => 'Seleccione el parentesco',
                    'maxlength' => 20,
                    'data-toggle' => 'tooltip',
                    'title' => 'Seleccione el parentesco con la persona de contacto',
                ],
            ])
            ->add('telefono', TextType::class, [
                'label' => 'Teléfono de contacto',
                'attr' => [
                    'class' => 'form-control mb-2',
                    'maxlength' => 15,
                    'data-toggle' => 'tooltip',
                    'title' => 'Ingrese el número telefónico de contacto',
                ],
            ])
            ->add('correo', TextType::class, [
                'label' => 'Correo electrónico',
                'attr' => [
                    'class' => 'form-control mb-2',
                    'maxlength' => 50,
                    'data-toggle' => 'tooltip',
                    'title' => 'Ingrese el correo electrónico de contacto',
                ],
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
