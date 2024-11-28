<?php

namespace App\Form;

use App\Entity\Panier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints as Assert;

class PanierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_panier', IntegerType::class, [
                'label' => 'Identifiant du panier',
                'attr' => ['class' => 'form-control'], // Ajout de classe CSS pour Bootstrap
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le champ Identifiant du panier ne peut pas être vide.',
                    ]),
                    new Assert\Positive([
                        'message' => 'L\'identifiant du panier doit être un nombre positif.',
                    ]),
                ],
            ])
            ->add('id_produit', IntegerType::class, [
                'label' => 'Identifiant du produit',
                'attr' => ['class' => 'form-control'], // Ajout de classe CSS pour Bootstrap
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le champ Identifiant du produit ne peut pas être vide.',
                    ]),
                    new Assert\Positive([
                        'message' => 'L\'identifiant du produit doit être un nombre positif.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Panier::class,
        ]);
    }
}
