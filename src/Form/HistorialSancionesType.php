<?php

namespace App\Form;

use App\Entity\HistorialSanciones;
use App\Entity\InformacionPersonal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class HistorialSancionesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('informacionPersonal', EntityType::class, [
                'class' => InformacionPersonal::class,
                'choice_label' => 'nombre',
                'label' => 'Empleado',
                'placeholder' => 'Seleccione un empleado',
            ])
            ->add('fecha', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha de sanción',
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'La fecha no puede ser futura.',
                    ]),
                ],
            ])
            ->add('motivo', TextType::class, [
                'label' => 'Motivo',
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción',
                'attr' => ['rows' => 4],
            ])
            ->add('comprovante', FileType::class, [
                'label' => 'Comprobante (opcional)',
                'mapped' => false, // No está ligado directamente a la entidad
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Solo se permiten archivos PDF o imágenes (JPG, PNG).',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HistorialSanciones::class,
        ]);
    }
}
