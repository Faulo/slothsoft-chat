<?php
namespace Slothsoft\Farah;

use Slothsoft\Chat\Model;
use Slothsoft\DBMS\DatabaseException;
use DOMDocument;
use Slothsoft\Farah\Module\Assets\AssetInterface;

return function(AssetInterface $asset) {
    $args = $asset->getArguments();
    
    $httpRequest = Kernel::getInstance()->getRequest();
    
    $dbName = 'cms';
    $tableName = 'minecraft_log';
    
    if ($name = $httpRequest->getInputValue('chat-database') and $name !== $tableName) {
        $dbName = 'chat';
        $tableName = $name;
    }
    
    //TOOD: enable mysql
    $dataDoc = new DOMDocument();
    $retNode = $dataDoc->createElement('range');
    $retNode->setAttribute('db-name', $dbName);
    $retNode->setAttribute('db-table', $tableName);
    $dataDoc->appendChild($retNode);
    return $dataDoc;
    
    $chat = new Model();
    try {
        $chat->init($dbName, $tableName);
    } catch(DatabaseException $e) {
        
    }
    $duration = (int) $httpRequest->getInputValue('chat-duration', 10 * 365);
    $end = $httpRequest->time;
    $start = $end - $duration * TIME_DAY;
    
    $dataDoc = new DOMDocument();
    $dataDoc->appendChild($chat->getRangeNode($start, $end, $dataDoc));
    return $dataDoc;
};