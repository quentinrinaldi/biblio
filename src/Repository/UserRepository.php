<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getPreferedCategory ($user)
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id) as count')
            ->addSelect('cat.name', 'name')
            ->addSelect('cat.id', 'catId')
            ->join('u.currentBorrowings', 'b')
            ->join('b.copy', 'c')
            ->join('c.document', 'd')
            ->join('d.categories', 'cat')
            ->where('u.id = :id')
            ->groupBy('cat.name')
            ->orderBy('count','DESC')
            ->setParameter('id', $user->getId())
            ->setFirstResult(0)
            ->setMaxResults(1);
    }

    public function getDelayedCount(User $user, $nbLastMonths) :int
    {
        return $this->createQueryBuilder('u')
            ->select('count(b.id)')
            ->join('u.currentBorrowings', 'b')
            ->where('u.id = :id')
            ->andWhere("b.status = 'RETURNED_LATE'")
            ->andWhere("b.expectedReturnDate >= DATE_ADD(CURRENT_DATE(), :nbMonths, 'month') ")
            ->setParameter('id', $user->getId())
            ->setParameter('nbMonths', '-'.$nbLastMonths)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getFinesCount($user, $nbLastMonths) :int
    {
        return $this->createQueryBuilder('u')
            ->select('count(p.id)')
            ->join('u.penalties', 'p')
            ->where('u.id = :id')
            ->andWhere("p.type = 'FINE'")
            ->andWhere("p.endDate >= DATE_ADD(CURRENT_DATE(), :nbMonths, 'month') ")
            ->setParameter('id', $user->getId())
            ->setParameter('nbMonths', '-'.$nbLastMonths)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
