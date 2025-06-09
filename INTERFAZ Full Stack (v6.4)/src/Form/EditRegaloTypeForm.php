<?php

namespace App\Form;

use App\Entity\Pago;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditRegaloTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('regalo', TextType::class, [
                'label' => 'Código Tarjeta Regalo',
                'required' => false,
                'attr' => [
                    'pattern' => '[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}', // 🔹 Formato alfanumérico con guiones
                    'inputmode' => 'text', // 🔹 Facilita la entrada en móviles
                    'maxlength' => 14, // 🔹 Limita el código a 14 caracteres con guiones
                    'placeholder' => 'Ejemplo: ABCD-1234-EFGH',
                ],
            ])
            ->add('activo', CheckboxType::class, [
                'label' => '¿Establecer como predeterminado?',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pago::class,
        ]);
    }
}
