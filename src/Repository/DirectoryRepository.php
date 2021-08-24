<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Directory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Directory|null find($id, $lockMode = null, $lockVersion = null)
 * @method Directory|null findOneBy(array $criteria, array $orderBy = null)
 * @method Directory[]    findAll()
 * @method Directory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DirectoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Directory::class);
    }

    // /**
    //  * @return Directory[] Returns an array of Directory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Directory
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */



    public function findByCompanyDivision($value)
    {
        return $this->createQueryBuilder('d')
            ->join('d.companyDivision', 'c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }


    
    public function findByCompanyDivisionWithNoParent($value)
    {
        return $this->createQueryBuilder('d')
            ->join('d.companyDivision', 'c')
            ->andWhere('c.id = :val')
            ->andWhere('d.parent IS NULL')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
    
    public function findByParent($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.parent = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findByCompanyWithNoParent(Company $company)
    {
        return $this->createQueryBuilder('d')
            ->join('d.companyDivision', 'c')
            ->join('c.company', 'e')
            ->andWhere('e.id = :val')
            ->andWhere('d.parent IS NULL')
            ->setParameter('val', $company)
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
