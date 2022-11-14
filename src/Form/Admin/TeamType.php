<?php

namespace App\Form\Admin;

use App\Entity\Admin\Structure;
use App\Entity\Admin\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('contactTel')
            ->add('contactMail')
            ->add('place')
            ->add('Anims')
            ->add('structures',EntityType::class, [
                'class' => Structure::class,
                'label' => 'Equipement du bien',
                'multiple' => true,
                'choice_attr' => ChoiceList::attr($this, function (?Structure $propertyEquipement) {
                    return $propertyEquipement ? ['data-data' => $propertyEquipement->getName()] : [];
                })
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
