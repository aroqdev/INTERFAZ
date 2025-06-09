<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RegisterUserTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('apellido')
            ->add('email')
            ->add('password')
            ->add('roles', ChoiceType::class, [
                'choices' => array_merge(['-Seleccionar-' => ''], [
                    'Usuario' => 'ROLE_USER',
                    'Vendedor' => 'ROLE_SELLER',
                ]),
                'expanded' => false, // Si quieres mostrarlo como un desplegable
                'multiple' => false, // Permitir solo un rol
                'label' => 'Seleccione su rol',
                'mapped' => false,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
