<?php

namespace App\Form;

use App\Entity\Pago;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PagoPaypalTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('paypal', EmailType::class, [
                'label' => 'Correo de PayPal',
                'required' => true,
                'attr' => [
                    'inputmode' => 'email', // 🔹 Facilita la entrada en móviles
                    'maxlength' => 50, // 🔹 Limita la longitud del correo
                    'placeholder' => 'Ejemplo: usuario@correo.com',
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
