<?php

namespace App\Repository;

use App\Entity\SalaryType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SalaryType|null find($id, $lockMode = null, $lockVersion = null)
 * @method SalaryType|null findOneBy(array $criteria, array $orderBy = null)
 * @method SalaryType[]    findAll()
 * @method SalaryType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalaryTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SalaryType::class);
    }

    // /**
    //  * @return SalaryType[] Returns an array of SalaryType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SalaryType
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
