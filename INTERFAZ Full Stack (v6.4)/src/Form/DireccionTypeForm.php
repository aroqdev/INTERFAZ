<?php

namespace App\Form;

use App\Entity\Direccion;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DireccionTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ciudad', TextType::class, ['label' => 'Ciudad'])
            ->add('calle', TextType::class, ['label' => 'Calle'])
            ->add('numero', TextType::class, ['label' => 'Número'])
            ->add('piso', TextType::class, ['label' => 'Piso (Opcional)', 'required' => false])
            ->add('codigo_postal', TextType::class, ['label' => 'Código Postal'])
            ->add('activo', CheckboxType::class, [
                'label' => '¿Establecer como predeterminada?',
                'required' => false,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Direccion::class,
        ]);
    }
}
