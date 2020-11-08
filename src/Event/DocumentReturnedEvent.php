<?php
namespace App\Event;



use App\Entity\Document;
use Symfony\Contracts\EventDispatcher\Event;

/*
 * The document.borrowed event is dispatched each time a document is borrowed by a user.
 */
class DocumentReturnedEvent extends Event
{
    public const NAME = 'document.returned';

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