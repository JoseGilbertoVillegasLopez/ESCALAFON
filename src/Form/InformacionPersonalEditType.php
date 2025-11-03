<?php

namespace App\Form;

use App\Entity\InformacionPersonal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File; // Validador para archivos

class InformacionPersonalEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'mapped' => false,                  // ⚠️ No está en la entidad
                'label' => 'Nombre completo',       // Etiqueta que verá el usuario
                'disabled' => true,                 // Solo lectura
                'data' => $options['data'] ? $options['data']->__toString() : '', // Usa el método __toString()
                'attr' => [
                    'class' => 'form-control text-center fw-bold',
        ],
            ])
            ->add('telefonoFijo')
            ->add('telefonoCelular')
            ->add('correo')
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

            ->add('imagen',FileType::class,[
                'mapped' => false, // ¡IMPORTANTE! No está mapeado a la propiedad "imagen" de la entidad
                'required' => false, // La imagen no es obligatoria para poder crear el registro
                'attr' => [ // Atributos HTML adicionales
                    'accept' => 'image/*', // Sugiere al navegador que solo permita imágenes
                    'onchange' => 'previewImage(event)', // Llama a una función JS para previsualizar
                ],
                'constraints' => [ // Reglas de validación del archivo
                    new File([ // Usamos la constraint File del componente Validator
                        'maxSize' => '5M', // Tamaño máximo permitido
                        'mimeTypes' => [ // Tipos MIME permitidos
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Solo se permiten imágenes JPEG, PNG o WEBP', // Mensaje de error
                    ]),
                ],
            ])
            //contactosEmergencias
            ->add('contactosEmergencias', CollectionType::class, [
                'entry_type' => ContactosEmergenciaType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])

            

            //capacitacion
            ->add('capacitacion', CollectionType::class, [
            'entry_type'   => CapacitacionType::class, // cada item es un subform de Capacitacion
            'entry_options'=> [
                'label' => false, // evita etiquetas repetidas en cada item
            ],
            'allow_add'    => true,   // podrás agregar más items luego con JS
            'allow_delete' => true,   // podrás eliminarlos
            'by_reference' => false,  // importante para que llame a addCapacitacion()
            'label'        => false,  // no muestres una etiqueta general
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
