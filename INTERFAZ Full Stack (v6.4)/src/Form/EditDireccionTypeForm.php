<?php

namespace App\Form;

use App\Entity\Direccion;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditDireccionTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ciudad', TextType::class, ['label' => 'Ciudad', 'required' => false,])
            ->add('calle', TextType::class, ['label' => 'Calle', 'required' => false,])
            ->add('numero', TextType::class, ['label' => 'Número', 'required' => false,])
            ->add('piso', TextType::class, ['label' => 'Piso (Opcional)', 'required' => false])
            ->add('codigo_postal', TextType::class, ['label' => 'Código Postal', 'required' => false,])
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
