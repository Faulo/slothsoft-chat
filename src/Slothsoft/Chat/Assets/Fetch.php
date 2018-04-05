<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Assets;

use Slothsoft\Chat\Model;
use Slothsoft\Core\Calendar\Seconds;
use Slothsoft\Core\DBMS\DatabaseException;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Node\Asset\AssetImplementation;
use Slothsoft\Farah\Module\Results\DOMDocumentResult;
use Slothsoft\Farah\Module\Results\ResultInterface;
use DOMDocument;

class Fetch extends AssetImplementation
{

    protected function loadResult(FarahUrl $url): ResultInterface
    {
        $args = $url->getArguments();
        
        $dbName = 'cms';
        $tableName = 'minecraft_log';
        
        if ($name = $args->get('chat-database') and $name !== $tableName) {
            $dbName = 'chat';
            $tableName = $name;
        }
        
        $dataDoc = new DOMDocument();
        // TOOD: enable mysql+chat
        if ($chatDisabled = false) {
            $retNode = $dataDoc->createElement('range');
            $retNode->setAttribute('db-name', $dbName);
            $retNode->setAttribute('db-table', $tableName);
            $dataDoc->appendChild($retNode);
        } else {
            $chat = new Model();
            try {
                $chat->init($dbName, $tableName);
            } catch (DatabaseException $e) {}
            $duration = (int) $args->get('chat-duration', 1);
            $end = time();
            $start = $end - $duration * Seconds::DAY;
            
            $dataDoc->appendChild($chat->getRangeNode($start, $end, $dataDoc));
        }
        return new DOMDocumentResult($url, $dataDoc);
    }
}

