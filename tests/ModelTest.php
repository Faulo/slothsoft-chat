<?php
declare(strict_types = 1);
namespace Slothsoft\Chat;

use PHPUnit\Framework\TestCase;

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
            public function select(): ?array {
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
}
