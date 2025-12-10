<?php

namespace App\Form;

use App\Entity\FormacionAcademica;
use App\Entity\InformacionPersonal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File; // Validador para archivos


class FormacionAcademicaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('escolaridad', TextType::class, [
                'label' => 'Nivel de Escolaridad',
                'attr' => ['class' => 'form-control mb-2'],
                'required' => false,
            ])
                        ->add('certificado', FileType::class, [
                'label' => 'Certificado (PDF o imagen)',
                'mapped' => false,
                'required' => false, // 👈 OBLIGATORIO
                'attr' => [
                    'accept' => '.pdf,image/*',
                    'class' => 'form-control mb-2'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'],
                        'mimeTypesMessage' => 'Solo se permiten archivos PDF, JPG o PNG.',
                    ])
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FormacionAcademica::class,
        ]);
    }
}
