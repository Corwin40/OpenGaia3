<?php

namespace App\Form\GestApp;

use App\Entity\Admin\Member;
use App\Entity\GestApp\Recommandation;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecommandationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('clientFirstname')
            ->add('clientLastname')
            ->add('clientAddress')
            ->add('clientComplement')
            ->add('clientZipcode')
            ->add('clientCity')
            ->add('clientEmail')
            ->add('clientDesk')
            ->add('clientGsm')
            ->add('member', EntityType::class, [
                'class' => Member::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.id', 'ASC');
                },
                'choice_label' => 'structure',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recommandation::class,
        ]);
    }
}
