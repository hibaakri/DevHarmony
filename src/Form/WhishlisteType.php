<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\User;
use App\Entity\Whishliste;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class WhishlisteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Items', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'Titre',
                'multiple' => true,
                'expanded' =>true ,                
            ])
            //  ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            //  ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Whishliste::class,
        ]);
    }

    
}
