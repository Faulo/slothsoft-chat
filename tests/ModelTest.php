<?php
declare(strict_types = 1);

namespace Slothsoft\Chat;

use PHPUnit\Framework\TestCase;
use RuntimeException;

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
            public function useDatabaseTable(object $table): void {
                $this->dbmsTable = $table;
            }
        };
        $model->useDatabaseTable(new class() {
            public function select(): null {
                return null;
            }
        });
        
        $document = new \DOMDocument();
        $range = $model->getRangeNode(0, time(), $document);
        
        $this->assertSame('range', $range->tagName);
        $this->assertSame('chat', $range->getAttribute('db-name'));
        $this->assertSame('messages', $range->getAttribute('db-table'));
        $this->assertFalse($range->hasChildNodes());
    }

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
     */
    public function testMinecraftInsertListsAllMissingRconSettings(array $environment, array $missing): void {
        foreach (['MINECRAFT_RCON_ADDRESS', 'MINECRAFT_RCON_PORT', 'MINECRAFT_RCON_PASSWORD'] as $name) {
            putenv(array_key_exists($name, $environment) ? $name . '=' . $environment[$name] : $name);
        }
        $table = new class() {
            public function insert(array $message): void {
                throw new \LogicException('No message should be stored with missing RCON configuration.');
            }
        };
        $model = $this->createModelForInsert($table, true);

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
        $table = new class() {
            public array $messages = [];

            public function insert(array $message): void {
                $this->messages[] = $message;
            }
        };
        $model = $this->createModelForInsert($table, false);

        $model->insert('Hello', 123, '127.0.0.1');

        $this->assertSame([[
            'time' => 123,
            'message' => 'Hello',
            'ip' => '127.0.0.1',
            'type' => MinecraftLog::$messageTypes['rcon']
        ]], $table->messages);
    }

    private function createModelForInsert(object $table, bool $isMinecraft): Model {
        return new class($table, $isMinecraft) extends Model {
            public function __construct(object $table, bool $isMinecraft) {
                parent::__construct('chat', $isMinecraft ? 'minecraft_log' : 'messages');
                $this->dbmsTable = $table;
                $this->isMinecraft = $isMinecraft;
            }
        };
    }
}
