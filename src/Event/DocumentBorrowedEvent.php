<?php
namespace App\Event;



use App\Entity\Document;
use Symfony\Contracts\EventDispatcher\Event;

/*
 * The document.borrowed event is dispatched each time a document is borrowed by a user.
 */
class DocumentBorrowedEvent extends Event
{
    public const NAME = 'document.borrowed';

    protected $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function getDocument()
    {
        return $this->document;
    }
}