<?php

namespace App\Security;

use App\Entity\Book;
use App\Entity\Borrowing;
use App\Entity\Document;
use App\Entity\User;
use App\Policy\PenaltyPolicy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DocumentVoter extends Voter
{
    // these strings are just invented: you can use anything
    const DOCUMENT = 'document';
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::DOCUMENT])) {
            return false;
        }

        // only vote on `Post` objects
        if (!$subject instanceof Document) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $bookRepo = $this->em->getRepository(Book::class);

        if ($bookRepo->getRemainingCopiesOfDocument($subject) == 0) {
            return false;
        }
        return true;
    }

}