<?php

namespace App\Form;

use App\Entity\Pago;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditCuentaTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cuenta_corriente', TextType::class, [
                'label' => 'Número de Cuenta Corriente',
                'required' => false,
                'attr' => [
                    'pattern' => '[A-Z]{2}[0-9]{10,20}', // 🔹 Letras al inicio + números
                    'inputmode' => 'text', // 🔹 Permite letras en móviles
                    'maxlength' => 22, // 🔹 Máximo 22 caracteres (IBAN más largo)
                    'placeholder' => 'Ejemplo: ES76207503201234567890',
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
