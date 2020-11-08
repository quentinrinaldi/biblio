<?php

namespace App\Repository;

use App\Entity\Artist;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Category find($id, $lockMode = null, $lockVersion = null)
 * @method null|Category findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findDistinctCategoriesByArtist(Artist $artist)
    {
        return $this->createQueryBuilder('c')
            ->select('c.name')
            ->join('c.documents', 'd')
            ->join('d.artistInvolvements', 'i')
            ->join('i.artist', 'a')
            ->where('a.id = :artistID')
            ->setParameter('artistID', $artist->getId())
            ->distinct()
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllCategoriesNamesByBook()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllIdByBook($book)
    {
        return $this->createQueryBuilder('c')
            ->select('c.id')
            ->join('c.documents', 'd')
            ->where('d.id = :documentId')
            ->setParameter('documentId', $book->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Category[] Returns an array of Category objects
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
    public function findOneBySomeField($value): ?Category
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
