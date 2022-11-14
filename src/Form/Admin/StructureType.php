<?php

namespace App\Form\Admin;

use App\Entity\Admin\Structure;
use App\Entity\Admin\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class StructureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('respFirstName')
            ->add('respLastName')
            ->add('address')
            ->add('complement')
            ->add('zipcode')
            ->add('city')
            ->add('siret')
            ->add('ape')
            ->add('tvaNumber')
            ->add('urlWeb')
            ->add('urlFacebook')
            ->add('urlInstagram')
            ->add('urlLinkedin')
            ->add('EmailStruct')
            ->add('phoneDesk')
            ->add('phoneGsm')
            ->add('FirstActivity')
            ->add('secondActivity')
            ->add('projectDev')
            ->add('logoStructureFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer',
                'download_label' => 'Télecharger',
            ])
            ->add('illustrationFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer',
                'download_label' => 'Télecharger',
            ])
            ->add('illustrationtwoFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer',
                'download_label' => 'Télecharger',
            ])
            ->add('illustrationthirdFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer',
                'download_label' => 'Télecharger',
            ])
            ->add('Team',EntityType::class, [
                'class' => Team::class,
                'label' => 'Equipement du bien',
                'multiple' => true,
                'choice_attr' => ChoiceList::attr($this, function (?Team $team) {
                    return $team ? ['data-data' => $team->getName()] : [];
                }),
                'required' => false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Structure::class,
        ]);
    }
}
