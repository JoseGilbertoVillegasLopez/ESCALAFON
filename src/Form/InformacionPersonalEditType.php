<?php

namespace App\Form;

use App\Entity\InformacionPersonal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class InformacionPersonalEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 🧍 Imagen de perfil
            ->add('imagen', FileType::class, [
                'label' => 'Actualizar imagen',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/jpg', 'image/svg+xml'],
                        'mimeTypesMessage' => 'Solo se permiten imágenes JPG, PNG o SVG.',
                    ]),
                ],
            ])

            // 💍 Estado civil
            ->add('estadoCivil', ChoiceType::class, [
                'choices' => [
                    'Soltero(a)' => 'Soltero(a)',
                    'Casado(a)' => 'Casado(a)',
                    'Divorciado(a)' => 'Divorciado(a)',
                    'Viudo(a)' => 'Viudo(a)',
                ],
                'placeholder' => 'Selecciona tu estado civil',
                'label' => 'Estado civil',
            ])

            // 📞 Información de contacto
            ->add('telefonoFijo', TelType::class, [
                'label' => 'Teléfono fijo',
                'required' => false,
            ])
            ->add('telefonoCelular', TelType::class, [
                'label' => 'Teléfono celular',
                'required' => false,
            ])
            ->add('correo', EmailType::class, [
                'label' => 'Correo electrónico',
            ])
            ->add('domicilio', TextType::class, [
                'label' => 'Domicilio',
                'required' => false,
            ]);
            $builder->add('contactosEmergencias', CollectionType::class, [
                'entry_type' => ContactosEmergenciaType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InformacionPersonal::class,
        ]);
    }
}
