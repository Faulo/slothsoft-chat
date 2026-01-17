<?php
declare(strict_types = 1);
namespace Slothsoft\Chat;

use PHPUnit\Framework\TestCase;

/**
 * ModelTest
 *
 * @see Model
 *
 * @todo auto-generated
 */
final class ModelTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Model::class), "Failed to load class 'Slothsoft\Chat\Model'!");
    }
}