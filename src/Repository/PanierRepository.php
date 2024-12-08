<?php

namespace App\Repository;

use App\Entity\Panier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Panier>
 */
class PanierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Panier::class);
    }

    //    /**
    //     * @return Panier[] Returns an array of Panier objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Panier
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }




    /**
     * Calculer le total du panier avec une remise si applicable.
     */
    // public function calculerTotal(Panier $panier): float
    // {
    //     $total = 0;
    //     foreach ($panier->getProduits() as $produit) {
    //         $total += $produit->getPrix() * $produit->getQuantite();
    //     }

    //     // Appliquer une remise si le total dépasse un certain seuil
    //     if ($total > 150) {
    //         $total *= 0.9; // Remise de 10%
    //     }

    //     return $total;
    // }

    // /**
    //  * Calculer le total du panier avec les frais de livraison.
    //  */
    // public function calculerTotalAvecLivraison(Panier $panier): float
    // {
    //     $total = $this->calculerTotal($panier);

    //     // Calculer les frais de livraison
    //     $fraisLivraison = 7; // Frais fixes
    //     if ($total > 100) {
    //         $fraisLivraison = 0; // Gratuit si le total dépasse 100
    //     }

    //     return $total + $fraisLivraison;
    // }
}
