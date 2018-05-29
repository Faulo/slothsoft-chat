<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Assets;

use Slothsoft\Chat\Model;
use Slothsoft\Core\Calendar\Seconds;
use Slothsoft\Core\IO\Writable\DOMWriterDocumentFromElementTrait;
use DOMDocument;
use DOMElement;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;

class Fetch implements DOMWriterInterface
{
    use DOMWriterDocumentFromElementTrait;
    
    private $chat;
    private $duration;
    public function __construct(Model $chat, int $duration) {
        $this->chat = $chat;
        $this->duration = $duration;
    }
    public function toElement(DOMDocument $targetDoc) : DOMElement
    {
        $end = time();
        $start = $end - $this->duration * Seconds::DAY;
        
        return $this->chat->getRangeNode($start, $end, $targetDoc);
    }

}

