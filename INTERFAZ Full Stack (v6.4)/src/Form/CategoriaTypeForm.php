<?php

namespace App\Form;

use App\Entity\Categoria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CategoriaTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imagen', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'URL de la imagen',
                ],
                'label' => 'Imagen de la categoría',
            ])
            ->add('nombre', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'maxlength' => 50,
                    'placeholder' => 'Ingrese el nombre de la categoría',
                ],
                'label' => 'Nombre',
            ])
            ->add('descripcion', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Descripción breve de la categoría',
                ],
                'label' => 'Descripción',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categoria::class,
        ]);
    }
}
