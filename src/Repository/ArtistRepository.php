<?php

namespace App\Repository;

use App\Entity\Artist;
use App\Entity\Document;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Artist find($id, $lockMode = null, $lockVersion = null)
 * @method null|Artist findOneBy(array $criteria, array $orderBy = null)
 * @method Artist[]    findAll()
 * @method Artist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artist::class);
    }

    public function findArtistsByCategories(array $categories)
    {
        return $this->createQueryBuilder('a')
            ->join('a.artistInvolvements', 'i')
            ->join('i.document', 'd')
            ->join('d.categories', 'c')
            ->andWhere('c.name in (:categories)')
            ->setParameter('categories', $categories)
            ->distinct()
            ->getQuery()
            ->getResult()
            ;
    }

    public function findArtistsBornBefore(DateTime $date)
    {
        return $this->createQueryBuilder('a')
            ->where('a.birthdate < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findArtistsByRole(string $role)
    {
        return $this->createQueryBuilder('a')
            ->join('a.artistInvolvements', 'i')
            ->where('i.type LIKE :role')
            ->setParameter('role', $role)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findArtistWithMoreThanOneRole()
    {
        return $this->createQueryBuilder('b')
            ->join('b.artistInvolvements', 'p')
            ->groupBy('b.id')
            ->addGroupBy('p.role')
            ->having("COUNT('b.id') > 1")
            ->getQuery()
            ->getResult()
        ;
    }

    public function findArtistsIdByDocumentAndByRole(Document $document, $role)
    {
        return $this->createQueryBuilder('a')
            ->select('a.id')
            ->join('a.artistInvolvements', 'i')
            ->join('i.document', 'd')
            ->where('d.id = :documentID')
            ->andWhere('i.type = :role')
            ->setParameter('documentID', $document->getId())
            ->setParameter('role', $role)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Author[] Returns an array of Author objects
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
    public function findOneBySomeField($value): ?Artist
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
