<?php

namespace App\Form;

use App\Entity\Categoria;
use App\Entity\Producto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class PoductoSellerTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imagen', TextType::class, [
                'label' => 'URL de la imagen',
                'required' => true,
                'attr' => ['placeholder' => 'Ejemplo: https://mi-tienda.com/producto.jpg'],
            ])
            ->add('nombre', TextType::class, [
                'label' => 'Nombre del producto',
                'required' => true,
                'attr' => ['maxlength' => 255, 'placeholder' => 'Ejemplo: Auriculares Bluetooth'],
            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción',
                'required' => true,
                'attr' => ['rows' => 4, 'placeholder' => 'Ejemplo: Sonido envolvente y cancelación de ruido'],
            ])
            ->add('cantidad', IntegerType::class, [
                'label' => 'Cantidad disponible',
                'required' => true,
                'attr' => ['min' => 1, 'placeholder' => 'Ejemplo: 100'],
            ])
            ->add('precio', MoneyType::class, [
                'label' => 'Precio actual',
                'required' => true,
                'currency' => 'EUR',
                'attr' => ['min' => 0.01, 'placeholder' => 'Ejemplo: 49.99'],
            ])
            ->add('precio_anterior', MoneyType::class, [
                'label' => 'Precio anterior (si aplica)',
                'currency' => 'EUR',
                'required' => false,
                'attr' => ['placeholder' => 'Ejemplo: 59.99'],
            ])
            ->add('oferta', CheckboxType::class, [
                'label' => '¿Está en oferta?',
                'required' => false,
            ])
            ->add('idCat', EntityType::class, [
                'class' => Categoria::class,
                'choice_label' => 'nombre',
                'label' => 'Categoría',
                'required' => true,
                'placeholder' => '-Seleccionar-',
            ])
            ->add('activo', CheckboxType::class, [
                'label' => '¿Publicar este producto ahora?',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Producto::class,
        ]);
    }
}
