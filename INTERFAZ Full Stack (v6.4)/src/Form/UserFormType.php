<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'required' => false,
            ])
            ->add('apellido', TextType::class, [
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'required' => false,
            ])
            ->add('password', PasswordType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ingrese tu nueva contraseña',
                ],
                'mapped' => false,
            ])
            ->add('codigo_pais', ChoiceType::class, [
                'choices' => array_merge(['-Seleccionar-' => ''], [
                    'EE.UU. (+1)' => '+1',
                    'España (+34)' => '+34',
                    'Alemania (+49)' => '+49',
                    'Francia (+33)' => '+33',
                    'México (+52)' => '+52',
                ]),
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'label' => 'Código de país',
                'mapped' => false,
            ])
            ->add('telefono', TextType::class, [
                'required' => false,
                'attr' => [
                    'pattern' => '[0-9]{9,20}', // 🔹 Solo permite números (mínimo 9, máximo 20)
                    'placeholder' => 'Ingrese solo números',
                ],
                'label' => 'Número de teléfono',
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => array_merge(['-Seleccionar-' => ''], [
                    'Usuario' => 'ROLE_USER',
                    'Vendedor' => 'ROLE_SELLER',
                ]),
                'required' => false,
                'expanded' => false, // Si quieres mostrarlo como un desplegable
                'multiple' => false, // Permitir solo un rol
                'label' => 'Seleccione su rol',
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
