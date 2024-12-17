<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }
 

    public function searchByName(string $searchTerm)
    {
        return $this->createQueryBuilder('p')
            ->where('p.titre LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
    }


    public function findproducts(): array
    {
        return $this->createQueryBuilder('p')
             ->orderBy('p.id', 'DESC')
             ->getQuery()
            ->getResult()
        ;
    }

    public function findproductsPrixAsc(): array
    {
        return $this->createQueryBuilder('p')
             ->orderBy('p.prix', 'DESC')
             ->getQuery()
            ->getResult()
        ;
    }
    public function findproductsPrixDesc(): array
    {
        return $this->createQueryBuilder('p')
             ->orderBy('p.prix', 'ASC')
             ->getQuery()
            ->getResult()
        ;
    }

    public function findproductsByCategory (string $categoryTitle): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.category', 'c') // Join the category entity (assuming 'category' is the relationship name)
            ->where('c.titre = :categoryTitle') // Filter by category title
            ->setParameter('categoryTitle', $categoryTitle) // Bind the parameter
          
            ->getQuery()
            ->getResult();
    }


 
   /**
    * @return Produit[] Returns an array of Produit objects
    */
   public function findByExampleField($value): array
   {
       return $this->createQueryBuilder('p')
           ->andWhere('p.exampleField = :val')
           ->setParameter('val', $value)
           ->orderBy('p.id', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
 
 }
