<?php

namespace App\Security;

use App\Entity\Book;
use App\Entity\Borrowing;
use App\Entity\User;
use App\Policy\PenaltyPolicy;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class BorrowingVoter extends Voter
{
    // these strings are just invented: you can use anything
    const BORROW = 'borrow';
    private $policy;

    public function __construct(PenaltyPolicy $policy)
    {
        $this->policy = $policy;
    }
    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::BORROW])) {
            return false;
        }

        // only vote on `Post` objects
        if (!$subject instanceof Book) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (!$this->policy->checkQuota($user)){
            return false;
        }
            //return false;

        if (!$this->policy->checkBorrowingRight($user)) {
            return false;
        }
        return true;
    }

}