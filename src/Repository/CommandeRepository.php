<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    //    /**
    //     * @return Commande[] Returns an array of Commande objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Commande
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


   public function findCommandesByUser(int $userId): array  
    //QueryBuilder pour récupérer les commandes liées à un utilisateur spécifique.
   {
       return $this->createQueryBuilder('c') // 'c' est un alias pour Commande
           ->leftJoin('c.produits', 'p')     // Suppose que `produits` est la relation entre `Commande` et `Produit`
           ->addSelect('p')                 // Inclure les produits dans la requête
           ->where('c.user = :userId')
           ->setParameter('userId', $userId)
           ->orderBy('c.dateCommande', 'DESC') // Tri par date descendante
           ->getQuery()
           ->getResult(); // Retourne un tableau d'objets Commande
   }
}
