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

class SSEServer extends Server {
    
    private Model $model;
    
    public function __construct(string $tableName, string $dbName, Model $model) {
        parent::__construct($tableName, $dbName);
        
        $this->model = $model;
    }
    
    /** @return list<array{id: int, type: 'message', data: string|false}> */
    public function fetchNewEvents(int $lastId): array {
        $messages = $this->model->getMessageList($lastId);
        return $messages === [] ? [] : [$this->parseEventList($messages)];
    }
    
    /** @return array{id: int, type: 'message', data: string|false}|null */
    public function fetchLastEvent(): ?array {
        $message = parent::fetchLastEvent();
        return $message === null ? null : $this->parseEventList([$message]);
    }
    
    /**
     * @param array<array<array-key, mixed>> $eventList
     * @return array{id: int, type: 'message', data: string|false}
     */
    private function parseEventList(array $eventList): array {
        $doc = $this->model->createRangeDocument($eventList);
        return [
            'id' => (int) $doc->documentElement?->getAttribute('last-id'),
            'type' => 'message',
            'data' => $doc->saveXML()
        ];
    }
}
