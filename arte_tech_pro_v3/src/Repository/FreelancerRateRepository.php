<?php

namespace App\Repository;

use App\Entity\FreelancerRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FreelancerRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method FreelancerRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method FreelancerRate[]    findAll()
 * @method FreelancerRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FreelancerRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FreelancerRate::class);
    }

    // /**
    //  * @return FreelancerRate[] Returns an array of FreelancerRate objects
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
    public function findOneBySomeField($value): ?FreelancerRate
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
