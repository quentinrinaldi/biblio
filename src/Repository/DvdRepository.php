<?php

namespace App\Repository;

use App\Entity\Dvd;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Dvd find($id, $lockMode = null, $lockVersion = null)
 * @method null|Dvd findOneBy(array $criteria, array $orderBy = null)
 * @method Dvd[]    findAll()
 * @method Dvd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DvdRepository extends DocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, 'Dvd::class');
        $this->childClass = 'dvd';
    }

    // /**
    //  * @return Dvd[] Returns an array of Dvd objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Dvd
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
