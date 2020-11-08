<?php

namespace App\EventSubscriber;

use App\Entity\Book;
use App\Event\DocumentBorrowedEvent;
use App\Event\DocumentReturnedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BorrowingSubscriber implements EventSubscriberInterface
{

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            DocumentBorrowedEvent::class => 'onDocumentBorrowedEvent',
            DocumentReturnedEvent::class => 'onDocumentReturnedEvent'
        ];
    }

    public function onDocumentBorrowedEvent($event)
    {
        $bookRepo = $this->em->getRepository(Book::class);
        $document = $event->getDocument();
        $remainingCopies = $bookRepo->getRemainingCopiesOfDocument($document);
        if ($remainingCopies == 0) {
            $document->setAvailability(false);
            $this->em->flush();
        }
    }

    public function onDocumentReturnedEvent($event)
    {

        $document = $event->getDocument();
        $document->setAvailability(true);
        dump($document);
        $this->em->flush();
    }
}
