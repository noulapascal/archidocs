<?php

namespace App\Repository;

use App\Entity\CompanyDivision;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyDivision|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyDivision|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyDivision[]    findAll()
 * @method CompanyDivision[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyDivisionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyDivision::class);
    }

    public function findByCompany($value)
    {
        return $this->createQueryBuilder('c')
            ->join('c.company','e')
            ->andWhere('e.id = :val')
            ->setParameter('val', $value)
;
    }

    // /**
    //  * @return CompanyDivision[] Returns an array of CompanyDivision objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CompanyDivision
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
