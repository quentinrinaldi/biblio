<?php
namespace App\Recommendation;

use App\Entity\Book;
use App\Entity\User;
use Doctrine\ORM\EntityManager;

class RecommendationService
{

    private $em;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getRecommendation(User $user)
    {
        $userRepo = $this->em->getRepository(User::class);
        $userPref = $userRepo->getPreferedCategory($user);

        $bookRepo = $this->em->getRepository(Book::class);
        $similarUsers = [];

        foreach ($userRepo->findAll() as $otherUser)
        {
            $similarUserPref = $userRepo->getPreferedCategory($otherUser);
            if ($similarUserPref['catId'] == $userPref['catId'])
                array_push($similarUsers, $user->getId());
        }
        $bookRepo->findBookRecommendationByCategory($userPref['catId'], $similarUsers);
    }
}