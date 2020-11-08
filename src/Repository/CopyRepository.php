<?php

namespace App\Repository;

use App\Entity\Copy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Copy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Copy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Copy[]    findAll()
 * @method Copy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CopyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Copy::class);
    }

    public function findFirstAvailableCopy($book)
    {
        return $this->createQueryBuilder('c')
            ->where('c.document = :documentID')
            ->andWhere('c.status = :documentStatus')
            ->setParameter('documentID', $book->getId())
            ->setParameter('documentStatus', Copy::STATUS_AVAILABLE)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }

    // /**
    //  * @return Copy[] Returns an array of Copy objects
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
    public function findOneBySomeField($value): ?Copy
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
