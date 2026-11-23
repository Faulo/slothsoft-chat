<?php
declare(strict_types = 1);

namespace Slothsoft\Chat;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Slothsoft\Core\DBMS\Client;
use Slothsoft\Core\DBMS\Database;
use Slothsoft\Core\DBMS\Table;

/**
 * ModelTest
 *
 * @see Model
 *
 */
final class ModelTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Model::class), "Failed to load class 'Slothsoft\Chat\Model'!");
    }
    
    public function testEmptyDatabaseResultCreatesEmptyRange(): void {
        $model = new class('chat', 'messages') extends Model {
            protected function selectMessageRows(string $query, string $suffix): null {
                return null;
            }
        };
        
        $document = new \DOMDocument();
        $range = $model->getRangeNode(0, time(), $document);
        
        $this->assertSame('range', $range->tagName);
        $this->assertSame('chat', $range->getAttribute('db-name'));
        $this->assertSame('messages', $range->getAttribute('db-table'));
        $this->assertFalse($range->hasChildNodes());
    }
    
    /** @return iterable<string, array{array<string, string>, list<string>}> */
    public function missingRconConfigurationProvider(): iterable {
        yield 'all unset' => [[], ['MINECRAFT_RCON_ADDRESS', 'MINECRAFT_RCON_PORT', 'MINECRAFT_RCON_PASSWORD']];
        yield 'address unset' => [['MINECRAFT_RCON_PORT' => '25575', 'MINECRAFT_RCON_PASSWORD' => 'secret'], ['MINECRAFT_RCON_ADDRESS']];
        yield 'port unset' => [['MINECRAFT_RCON_ADDRESS' => 'localhost', 'MINECRAFT_RCON_PASSWORD' => 'secret'], ['MINECRAFT_RCON_PORT']];
        yield 'password unset' => [['MINECRAFT_RCON_ADDRESS' => 'localhost', 'MINECRAFT_RCON_PORT' => '25575'], ['MINECRAFT_RCON_PASSWORD']];
        yield 'empty settings' => [['MINECRAFT_RCON_ADDRESS' => '', 'MINECRAFT_RCON_PORT' => '', 'MINECRAFT_RCON_PASSWORD' => ''], ['MINECRAFT_RCON_ADDRESS', 'MINECRAFT_RCON_PORT', 'MINECRAFT_RCON_PASSWORD']];
        yield 'zero password is set' => [['MINECRAFT_RCON_PASSWORD' => '0'], ['MINECRAFT_RCON_ADDRESS', 'MINECRAFT_RCON_PORT']];
    }
    
    /**
     * @dataProvider missingRconConfigurationProvider
     * @runInSeparateProcess
     * @param array<string, string> $environment
     * @param list<string> $missing
     */
    public function testMinecraftInsertListsAllMissingRconSettings(array $environment, array $missing): void {
        foreach (['MINECRAFT_RCON_ADDRESS', 'MINECRAFT_RCON_PORT', 'MINECRAFT_RCON_PASSWORD'] as $name) {
            putenv(array_key_exists($name, $environment) ? $name . '=' . $environment[$name] : $name);
        }
        $model = new class('chat', 'minecraft_log') extends Model {
            public function init(): void {
                $this->dbmsTable = new Table(new Database(new Client(), $this->dbName), $this->dbTable);
            }
            
            protected function storeMessage(array $message): never {
                throw new \LogicException('No message should be stored with missing RCON configuration.');
            }
        };
        $model->init();
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing Minecraft RCON environment variables: ' . implode(', ', $missing));
        $model->insert('Hello', 123, '127.0.0.1');
    }
    
    /**
     * @runInSeparateProcess
     */
    public function testRegularChatDoesNotRequireRconSettings(): void {
        foreach (['MINECRAFT_RCON_ADDRESS', 'MINECRAFT_RCON_PORT', 'MINECRAFT_RCON_PASSWORD'] as $name) {
            putenv($name);
        }
        $model = new class('chat', 'messages') extends Model {
            /** @var list<array{time: int, message: string, ip: string, type: int}> */
            public array $messages = [];
            
            public function init(): void {
                $this->dbmsTable = new Table(new Database(new Client(), $this->dbName), $this->dbTable);
            }
            
            protected function storeMessage(array $message): void {
                $this->messages[] = $message;
            }
        };
        $model->init();
        
        $model->insert('Hello', 123, '127.0.0.1');
        
        $this->assertSame([[
            'time' => 123,
            'message' => 'Hello',
            'ip' => '127.0.0.1',
            'type' => MinecraftLog::$messageTypes['rcon']
        ]], $model->messages);
    }
}
