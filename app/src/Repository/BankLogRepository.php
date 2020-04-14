<?php

namespace App\Repository;

use App\Entity\BankLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BankLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method BankLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method BankLog[]    findAll()
 * @method BankLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BankLogRepository extends ServiceEntityRepository
{
    /**
     *  Contain generic methods
     */
    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BankLog::class);
    }

    // /**
    //  * @return BankLog[] Returns an array of BankLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BankLog
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
