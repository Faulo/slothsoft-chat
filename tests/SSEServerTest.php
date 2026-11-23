<?php
declare(strict_types = 1);

namespace Slothsoft\Chat;

use DOMDocument;
use PHPUnit\Framework\TestCase;

/**
 * SSEServerTest
 *
 * @see SSEServer
 *
 */
final class SSEServerTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(SSEServer::class), "Failed to load class 'Slothsoft\Chat\SSEServer'!");
    }
    
    public function testBatchUsesLastMessageIdAndRendersNullableSpeaker(): void {
        $model = new class('chat', 'messages') extends Model {
            protected function selectMessageRows(string $query, string $suffix): array {
                return [
                    ['id' => 41, 'time' => 123, 'message' => 'Hello', 'speaker' => null, 'ip' => '127.0.0.1'],
                    ['id' => '42', 'time' => '124', 'message' => 'Hi', 'speaker' => 'Alex', 'ip' => '']
                ];
            }
        };
        $server = new SSEServer('messages', 'chat', $model);
        
        $events = $server->fetchNewEvents(40);
        
        $this->assertCount(1, $events);
        $this->assertSame(42, $events[0]['id']);
        $this->assertSame('message', $events[0]['type']);
        $this->assertIsString($events[0]['data']);
        $document = new DOMDocument();
        $this->assertTrue($document->loadXML($events[0]['data']));
        $this->assertSame('124', $document->documentElement?->getAttribute('last-time'));
        $this->assertSame('Hello', $document->getElementsByTagName('p')->item(0)?->textContent);
        $this->assertSame('<Alex> Hi', $document->getElementsByTagName('p')->item(1)?->textContent);
    }
}
