<?php

namespace App\Repository;

use App\Entity\ServiceApresVente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServiceApresVente>
 */
class ServiceApresVenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceApresVente::class);
    }


    public function all(int $id): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.createdby = :id') // Assuming the relationship is correctly mapped
            ->setParameter('id', $id)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function alladmin (): array
    {
        return $this->createQueryBuilder('p')
        ->orderBy('p.id', 'DESC')
          
            ->getQuery()
            ->getResult();
    }









    //    /**
    //     * @return ServiceApresVente[] Returns an array of ServiceApresVente objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ServiceApresVente
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
