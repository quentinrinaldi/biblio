<?php
namespace App\Policy;

use App\Entity\Borrowing;
use App\Entity\Copy;
use App\Entity\Penalty;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;

class PenaltyPolicy
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function doReturnDocument(Borrowing $borrowing) :array
    {

        $userRepository= $this->em->getRepository(User::class);
        $user = $borrowing->getUser();
        $today = new \DateTime('now');

        if ($borrowing->getExpectedReturnDate() > $today) {
            $borrowing->setStatus('RETURNED_OK');
            $ReturnResult = ['status' => 'success', 'message' => 'The document has been correctly returned'];
        }
        else {
            $borrowing->setStatus('RETURNED_LATE');
            if ($userRepository->getDelayedCount($user, 6) == 2) {
                $ReturnResult = ['status' => 'warning', 'message' => 'You returned the document too late. Next time you\'ll have a fine'];
            }
            else if ($userRepository->getDelayedCount($user, 3) == 3) {
                $this->makeFine($user, 1);
                $ReturnResult = ['status' => 'error', 'message' => 'You returned the document too late for the third time. You have to pay a fine of 1€'];
            }
            else if ($userRepository->getFinesCount($user, 6) == 2) {
                $this->makeFine($user, 5);
                $ReturnResult = ['status' => 'error', 'message' => 'You returned the document too late for the third time. You have to pay a fine of 5€'];

            }
            else if ($userRepository->getFinesCount($user, 12) == 3 && $userRepository->getDelayedCount($user, 3) == 10) {
                $this->makeExclusion($user);
                $ReturnResult = ['status' => 'error', 'message' => 'You returned the document too late for the third time. You can\'t borrow any document for 14 days'];
            }
            else {
                $ReturnResult = ['status' => 'warning', 'message' => 'You returned the document too late. Next time you\'ll have a fine'];
            }
        }
        $borrowing->getCopy()->setStatus('AVAILABLE');
        $borrowing->getCopy()->getDocument()->setAvailability(true);
        $borrowing->setReturnedAt($today);
        $this->em->flush();
        return $ReturnResult;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function checkBorrowingRight(User $user) :bool
    {
        if ($user->getStatus() == 'EXPIRED_SUBSCRIPTION')
            return false;

        foreach ($user->getPenalties() as $penalty)  {
            switch ($penalty->getType()) {
                case 'FINE':
                    if ($penalty->getPaymentStatus() == false)
                        return false;
                    break;
                case 'TEMPORARY_EXCLUSION':
                    $today = new DateTime('now');
                    if ($penalty->getEndDate() < $today)
                        return false;
                    break;
            }
        }
        return true;
    }

    public function checkQuota(User $user):bool
    {
        $borrowingRepository= $this->em->getRepository(Borrowing::class);
        $userCurrentBorrowingsCount = $borrowingRepository->getCurrentBorrowingCountByUser($user);
        dump($userCurrentBorrowingsCount);
        if ($userCurrentBorrowingsCount > 9)
            return false;
        return true;
    }

    private function makeFine(User $user, int $amount)
    {
        $penalty = new Penalty();
        $penalty->setAmount($amount);
        $penalty->setType(Penalty::PENALTY_FINE);
        $penalty->setEndDate(new \DateTime('now'));
        $penalty->setUser($user);
        $user->setStatus('AWAITING_FINE_PAYMENT');
        $this->em->persist($penalty);
        $this->em->flush();
    }

    private function makeExclusion(User $user)
    {
        $penalty = new Penalty();
        $penalty->setType(Penalty::PENALTY_EXCLUSION);
        $today = new DateTime('now');
        $penalty->setEndDate($today->add(new \DateInterval('P14D')));
        $penalty->setUser($user);
        $user->setStatus('EXCLUSION_IN_PROGRESS');
        $this->em->persist($penalty);
        $this->em->flush();
    }
}