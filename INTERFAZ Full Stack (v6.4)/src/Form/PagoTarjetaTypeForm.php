<?php

namespace App\Form;

use App\Entity\Pago;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PagoTarjetaTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre_titular', TextType::class, [
                'label' => 'Nombre del Titular',
                'required' => true,
                'attr' => [
                    'pattern' => '^[a-zA-Z\s]+$', // 🔹 Solo letras y espacios
                    'maxlength' => 50,
                    'placeholder' => 'Ejemplo: Juan Pérez',
                ],
            ])
            ->add('numero_tarjeta', TextType::class, [
                'label' => 'Número de Tarjeta',
                'attr' => [
                    'pattern' => '[0-9]{16}', // 🔹 Solo números y exactamente 16 dígitos
                    'inputmode' => 'numeric',
                    'minlength' => 16,
                    'maxlength' => 16,
                    'placeholder' => 'Ejemplo: 1234567890123456',
                ],
            ])
            ->add('fecha_vencimiento', TextType::class, [
                'label' => 'Fecha de Vencimiento',
                'required' => true,
                'attr' => [
                    'pattern' => '(0[1-9]|1[0-2])/[0-9]{2}', // 🔹 Formato MM/YY
                    'inputmode' => 'numeric', // 🔹 Facilita entrada en móviles
                    'maxlength' => 5, // 🔹 Restringe a "MM/YY"
                    'placeholder' => 'Ejemplo: 08/27',
                ],
            ])
            ->add('cvv', TextType::class, [
                'label' => 'CVV',
                'attr' => [
                    'pattern' => '[0-9]{3}', // 🔹 Solo números y exactamente 3 dígitos
                    'inputmode' => 'numeric',
                    'minlength' => 3,
                    'maxlength' => 3,
                    'placeholder' => 'Ejemplo: 123',
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
