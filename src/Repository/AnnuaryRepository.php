<?php

namespace App\Repository;

use App\Entity\Annuary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annuary|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annuary|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annuary[]    findAll()
 * @method Annuary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnuaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annuary::class);
    }

    // /**
    //  * @return Annuary[] Returns an array of Annuary objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Annuary
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */




    public function findByCompany($value)
    {
        return $this->createQueryBuilder('a')
            ->join('a.division','d')
            ->join('d.company','c')
            ->andWhere('c = :val')
            ->orderBy('a.name', 'ASC')

            ->setParameter('val', $value)

            
            ;
    }


}
