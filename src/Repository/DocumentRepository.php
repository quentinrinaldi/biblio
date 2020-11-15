<?php

namespace App\Repository;

use App\Entity\Artist;
use App\Entity\Category;
use App\Entity\Copy;
use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method null|Document find($id, $lockMode = null, $lockVersion = null)
 * @method null|Document findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
abstract class DocumentRepository extends ServiceEntityRepository
{
    protected $childClass;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function findDocumentsByArtist(Artist $artist)
    {
        return $this->createQueryBuilder('d')
            ->join('d.artistInvolvements', 'inv')
            ->andWhere('inv.artist = :artistID')
            ->andWhere('d INSTANCE OF :class')
            ->setParameter('artistID', $artist->getId())
            ->setParameter('class', $this->childClass)
            ->orderBy('d.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findDocumentsCountByArtist(Artist $artist)
    {
        return $this->createQueryBuilder('d')
            ->select('count(inv.id)')
            ->join('d.artistInvolvements', 'inv')
            ->andWhere('inv.artist = :artistID')
            ->andWhere('d INSTANCE OF :class')
            ->setParameter('artistID', $artist->getId())
            ->setParameter('class', $this->childClass)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findDocumentsByTitle($title)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.title LIKE :title')
            ->andWhere('d INSTANCE OF :class')
            ->setParameter('title', $title)
            ->setParameter('class', $this->childClass)
            ->orderBy('d.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findNewestAvailableDocumentsSelection($nbMax)
    {
        return $this->createQueryBuilder('d')
            ->where('d.availability = True')
            ->andWhere('d INSTANCE OF :class')
            ->orderBy('d.availableSince', 'DESC')
            ->setParameter('class', $this->childClass)
            ->setMaxResults($nbMax)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPinnedAndAvailableDocumentsSelection(int $nbMax)
    {
        return $this->createQueryBuilder('d')
            ->where('d.isPinned = True')
            ->andWhere('d.availability = True')
            ->andWhere('d INSTANCE OF :class')
            ->orderBy('d.title', 'ASC')
            ->setMaxResults($nbMax)
            ->setParameter('class', $this->childClass)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getRemainingCopiesCountOfDocument(Document $document)
    {
        return $this->createQueryBuilder('d')
            ->select('count(c.id) as count')
            ->join('d.copies', 'c')
            ->where('d.id = :documentID')
            ->andWhere('c.status = :status')
            ->andWhere('d INSTANCE OF :class')
            ->setParameter('status', Copy::STATUS_AVAILABLE)
            ->setParameter('documentID', $document->getId())
            ->setParameter('class', $this->childClass)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findCurrentDocumentsBorrowedByUser($user)
    {
        return $this->createQueryBuilder('d')
            ->join('c.borrowings', 'borr')
            ->join('d.copies', 'c')
            ->join('borr.user', 'u')
            ->where('u.id = :userID')
            ->andWhere('d INSTANCE OF :class')
            ->andWhere('c.status = :copyStatus')
            ->setParameter('userID', $user->getId())
            ->setParameter('copyStatus', Copy::STATUS_BORROWED)
            ->setParameter('class', $this->childClass)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findDocumentRecommendationByCategory($categoryId, $users)
    {
        return $this->createQueryBuilder('d')
            ->addSelect('count(d.id) as count')
            ->join('d.categories', 'cat')
            ->join('d.copies', 'cop')
            ->join('cop.borrowings', 'bor')
            ->join('bor.user', 'u')
            ->where('cat.id = :categoryId')
            ->andWhere('d INSTANCE OF :class')
            ->andwhere('u.id in (:users)')
            ->setParameter('class', $this->childClass)
            ->groupBy('d')
            ->addGroupBy('cop')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findIfDocumentIsCurrentlyBorrowedByUser($user, $document)
    {
        return $this->createQueryBuilder('d')
            ->join('d.copies', 'c')
            ->join('c.borrowings', 'b')
            ->join('b.user', 'u')
            ->where('u.id = :userID')
            ->andWhere('b.status not in (:borrowingStatus)')
            ->andWhere('d.id = :documentID')
            ->andWhere('d INSTANCE OF :class')
            ->setParameter('userID', $user->getId())
            ->setParameter('documentID', $document->getId())
            ->setParameter('class', $this->childClass)
            ->setParameter('borrowingStatus', ['RETURNED_OK', 'RETURNED_LATE'])
            ->getQuery()
            ->getResult()
        ;
    }

    public function findDocumentsByCategory(Category $category)
    {
        return $this->createQueryBuilder('d')
            ->join('d.categories', 'c')
            ->where('c.id = :categoryID')
            ->andWhere('d INSTANCE OF :class')
            ->setParameter('categoryID', $category->getId())
            ->setParameter('class', $this->childClass)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findDocumentsByCategories(array $categories, int $nbMax)
    {
        return $this->createQueryBuilder('d')
            ->join('d.categories', 'c')
            ->where('c.id in (:categories)')
            ->andWhere('d INSTANCE OF :class')
            ->setParameter('categories', $categories)
            ->setParameter('class', $this->childClass)
            ->setMaxResults($nbMax)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findDocumentsByArtists(array $artists, int $nbMax)
    {
        return $this->createQueryBuilder('d')
            ->join('d.artistInvolvements', 'i')
            ->join('i.artist', 'a')
            ->where('a.id in (:artists)')
            ->andWhere('d INSTANCE OF :class')
            ->setParameter('artists', $artists)
            ->setParameter('class', $this->childClass)
            ->setMaxResults($nbMax)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findMostPopularAvailableDocumentsSelection(int $nbMax)
    {
        return $this->createQueryBuilder('d')
            ->join('d.copies', 'c')
            ->leftjoin('c.borrowings', 'b')
            ->groupBy('d.id')
            ->where('d.availability = true')
            ->andWhere('d INSTANCE OF :class')
            ->orderBy('count(b.id)', 'DESC')
            ->setParameter('class', $this->childClass)
            ->setMaxResults($nbMax)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findMostPopularDocumentsByArtist(Artist $artist, int $nbMax)
    {
        return $this->createQueryBuilder('d')
            ->join('d.copies', 'c')
            ->leftjoin('c.borrowings', 'b')
            ->join('d.artistInvolvements', 'i')
            ->join('i.artist', 'a')
            ->groupBy('d.id')
            ->where('a.id = :artistID')
            ->andWhere('d INSTANCE OF :class')
            ->orderBy('count(b.id)', 'DESC')
            ->setParameter('artistID', $artist->getId())
            ->setParameter('class', $this->childClass)
            ->setMaxResults($nbMax)
            ->getQuery()
            ->getResult()
        ;
    }
}
