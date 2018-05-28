<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Executables;

use Slothsoft\Chat\Model;
use Slothsoft\Core\Calendar\Seconds;
use Slothsoft\Core\IO\Writable\DOMWriterDocumentFromElementTrait;
use Slothsoft\Farah\Module\Executables\ExecutableDOMWriterBase;
use DOMDocument;
use DOMElement;

class Fetch extends ExecutableDOMWriterBase
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
