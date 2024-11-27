<?php

namespace App\Form;

use App\Entity\ServiceApresVente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceApresVenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Type_probleme')
            ->add('Description_probleme')
            ->add('image',FileType::class,[
                'label'=> 'insérer votre image',
                'mapped' => false,

            ])
            // ->add('Date_demande', null, [
            //     'widget' => 'single_text',
            // ])
            
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
