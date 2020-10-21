<?php

namespace App\Repository;

use App\Entity\ArtistInvolvement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArtistInvolvement|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArtistInvolvement|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArtistInvolvement[]    findAll()
 * @method ArtistInvolvement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtistInvolvementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArtistInvolvement::class);
    }

    // /**
    //  * @return ArtistInvolvement[] Returns an array of ArtistInvolvement objects
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
    public function findOneBySomeField($value): ?ArtistInvolvement
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
