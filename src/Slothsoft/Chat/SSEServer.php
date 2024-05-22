<?php
declare(strict_types = 1);
/**
 * *********************************************************************
 * Slothsoft\Chat\SSEServer v1.01 26.10.2017 © Daniel Schulz
 *
 * Changelog:
 * v1.01 26.10.2017
 * refactoring
 * v1.00 29.05.2014
 * public function __construct($serverName)
 * *********************************************************************
 */
namespace Slothsoft\Chat;

use Slothsoft\SSE\Server;

class SSEServer extends Server
{

    private $model;

    public function __construct(string $tableName, string $dbName, Model $model)
    {
        parent::__construct($tableName, $dbName);
        
        $this->model = $model;
    }

    public function dispatchEvent($type, $data, $time = null, $ip = null)
    {
        return $this->model->insert($data, $time, $ip);
    }

    public function fetchNewEvents($lastId): iterable
    {
        if ($ret = $this->model->getMessageList($lastId)) {
            return [
                $this->_parseEventList(...$ret)
            ];
        } else {
            return [];
        }
    }

    public function fetchLastEvent()
    {
        if ($ret = parent::fetchLastEvent()) {
            $ret = $this->_parseEventList($ret);
        }
        return $ret;
    }

    private function _parseEventList(array ...$eventList): array
    {
        $doc = $this->model->createRangeDocument($eventList);
        return [
            'id' => (int) $doc->documentElement->getAttribute('last-id'),
            'type' => 'message',
            'data' => $doc->saveXML()
        ];
    }
}