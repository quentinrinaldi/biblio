<?php

namespace App\Repository;

use App\Entity\Artist;
use App\Entity\ArtistInvolvement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|ArtistInvolvement find($id, $lockMode = null, $lockVersion = null)
 * @method null|ArtistInvolvement findOneBy(array $criteria, array $orderBy = null)
 * @method ArtistInvolvement[]    findAll()
 * @method ArtistInvolvement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtistInvolvementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArtistInvolvement::class);
    }

    public function findDisctinctRolesByArtist(Artist $artist)
    {
        return $this->createQueryBuilder('a')
            ->select('a.type')
            ->where('a.artist = :artistID')
            ->groupBy('a.type')
            ->setParameter('artistID', $artist->getId())
            ->getQuery()
            ->getResult()
            ;
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
