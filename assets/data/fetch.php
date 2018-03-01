<?php
namespace Slothsoft\Chat;

use Slothsoft\DBMS\DatabaseException;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use DOMDocument;

return function(FarahUrl $url) {
    $args = $url->getArguments();
    
    $dbName = 'cms';
    $tableName = 'minecraft_log';
    
    if ($name = $args->get('chat-database') and $name !== $tableName) {
        $dbName = 'chat';
        $tableName = $name;
    }
    
    $dataDoc = new DOMDocument();
    //TOOD: enable mysql+chat
    if ($chatDisabled = true) {
        $retNode = $dataDoc->createElement('range');
        $retNode->setAttribute('db-name', $dbName);
        $retNode->setAttribute('db-table', $tableName);
        $dataDoc->appendChild($retNode);
    } else {
        $chat = new Model();
        try {
            $chat->init($dbName, $tableName);
        } catch(DatabaseException $e) {
            
        }
        $duration = (int) $args->get('chat-duration', 10 * 365);
        $end = time();
        $start = $end - $duration * TIME_DAY;
        
        $dataDoc->appendChild($chat->getRangeNode($start, $end, $dataDoc));
    }
    return $dataDoc;
};