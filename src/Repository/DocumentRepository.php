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
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function findDocumentsByArtist(Artist $artist)
    {
        return $this->createQueryBuilder('d')
            ->join('d.artistInvolvements', 'inv')
            ->andWhere('inv.artist = :artistID')
            ->setParameter('artistID', $artist->getId())
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
            ->setParameter('artistID', $artist->getId())
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findDocumentsByTitle($title)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.title LIKE :title')
            ->setParameter('title', $title)
            ->orderBy('d.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findNewestAvailableDocumentsSelection($nbMax)
    {
        return $this->createQueryBuilder('d')
            ->where('d.availability = True')
            ->orderBy('d.availableSince', 'DESC')
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
            ->orderBy('d.title', 'ASC')
            ->setMaxResults($nbMax)
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
            ->setParameter('status', Copy::STATUS_AVAILABLE)
            ->setParameter('documentID', $document->getId())
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
            ->andWhere('c.status = :copyStatus')
            ->setParameter('userID', $user->getId())
            ->setParameter('copyStatus', Copy::STATUS_BORROWED)
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
            ->andwhere('u.id in (:users)')
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
            ->setParameter('userID', $user->getId())
            ->setParameter('documentID', $document->getId())
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
            ->setParameter('categoryID', $category->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    public function findDocumentsByCategories(array $categories, int $nbMax)
    {
        return $this->createQueryBuilder('d')
            ->join('d.categories', 'c')
            ->where('c.id in (:categories)')
            ->setParameter('categories', $categories)
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
            ->where('a.id in (:authors)')
            ->setParameter('authors', $artists)
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
            ->orderBy('count(b.id)', 'DESC')
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
            ->orderBy('count(b.id)', 'DESC')
            ->setParameter('artistID', $artist->getId())
            ->setMaxResults($nbMax)
            ->getQuery()
            ->getResult()
        ;
    }
}
