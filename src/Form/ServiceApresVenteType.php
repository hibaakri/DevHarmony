<?php

namespace App\Form;

use App\Entity\ServiceApresVente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceApresVenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Type_probleme')
            ->add('Description_probleme')
            ->add('Date_demande', null, [
                'widget' => 'single_text',
            ])
            ->add('Etat_demande')
            ->add('Date_resolution', null, [
                'widget' => 'single_text',
            ])
            ->add('Commentaire_technicien')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ServiceApresVente::class,
        ]);
    }
}
