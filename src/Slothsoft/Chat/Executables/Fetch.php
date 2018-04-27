<?php
namespace Slothsoft\Chat\Executables;

use Slothsoft\Chat\Model;
use Slothsoft\Core\Calendar\Seconds;
use Slothsoft\Core\DBMS\DatabaseException;
use Slothsoft\Core\IO\Writable\DOMWriterDocumentFromElementTrait;
use Slothsoft\Farah\Module\Executables\ExecutableDOMWriterBase;
use DOMDocument;
use DOMElement;

class Fetch extends ExecutableDOMWriterBase
{
    use DOMWriterDocumentFromElementTrait;
    
    private $dbName;
    private $tableName;
    private $duration;
    public function __construct(string $dbName, string $tableName, int $duration) {
        $this->dbName = $dbName;
        $this->tableName = $tableName;
        $this->duration = $duration;
    }
    public function toElement(DOMDocument $targetDoc) : DOMElement
    {
        $chat = new Model();
        try {
            $chat->init($this->dbName, $this->tableName);
        } catch (DatabaseException $e) {
            
        }
        
        $end = time();
        $start = $end - $this->duration * Seconds::DAY;
        
        return $chat->getRangeNode($start, $end, $targetDoc);
    }

}

