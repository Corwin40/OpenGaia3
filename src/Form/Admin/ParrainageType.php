<?php

namespace App\Form\Admin;

use App\Entity\Admin\Parrainage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParrainageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parrainageFirstname')
            ->add('parrainageLastname')
            ->add('parrainageEmail')
            ->add('nameSociety')
            ->add('phoneDesk')
            ->add('phoneGsm')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Parrainage::class,
        ]);
    }
}
