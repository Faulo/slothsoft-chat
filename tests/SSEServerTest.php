<?php
declare(strict_types = 1);
namespace Slothsoft\Chat;

use PHPUnit\Framework\TestCase;

/**
 * SSEServerTest
 *
 * @see SSEServer
 *
 * @todo auto-generated
 */
class SSEServerTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(SSEServer::class), "Failed to load class 'Slothsoft\Chat\SSEServer'!");
    }
}