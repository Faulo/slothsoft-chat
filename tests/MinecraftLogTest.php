<?php
declare(strict_types = 1);

namespace Slothsoft\Chat;

use PHPUnit\Framework\TestCase;

/**
 * MinecraftLogTest
 *
 * @see MinecraftLog
 *
 */
final class MinecraftLogTest extends TestCase {

    public function testClassExists(): void {
        $this->assertTrue(class_exists(MinecraftLog::class), "Failed to load class 'Slothsoft\Chat\MinecraftLog'!");
    }

    public function testParseLogFormatsAndInvalidLines(): void {
        $table = new class() {
            public array $updated = [];

            public array $deleted = [];

            public function select(): array {
                return [
                    ['id' => 1, 'time' => 0, 'raw' => '2012-12-18 12:27:25 [INFO] <Alex> Hello'],
                    ['id' => 2, 'time' => 0, 'raw' => '[12:27:25] [Server thread/INFO]: <Steve> Hi'],
                    ['id' => 3, 'time' => 0, 'raw' => 'invalid line'],
                    ['id' => 4, 'time' => 0, 'raw' => '2012-12-18 12:28:00 [INFO] Alex lost connection: Disconnected']
                ];
            }

            public function update(array $line, int $id): void {
                $this->updated[$id] = $line;
            }

            public function delete(int $id): void {
                $this->deleted[] = $id;
            }
        };
        $log = new class() extends MinecraftLog {
            public function useDatabaseTable(object $table): void {
                $this->dbmsTable = $table;
            }
        };
        $log->useDatabaseTable($table);
        $today = getdate();

        $counts = $log->parse();

        $this->assertSame([
            MinecraftLog::$messageTypes['chat'] => 2,
            MinecraftLog::$messageTypes['invalid'] => 1,
            MinecraftLog::$messageTypes['logout'] => 1
        ], $counts);
        $this->assertSame([3], $table->deleted);
        $this->assertSame([1, 2, 4], array_keys($table->updated));
        $this->assertSame(mktime(12, 27, 25, 12, 18, 2012), $table->updated[1]['time']);
        $this->assertSame('Alex', $table->updated[1]['speaker']);
        $this->assertSame('Hello', $table->updated[1]['message']);
        $this->assertSame(mktime(12, 27, 25, $today['mon'], $today['mday'], $today['year']), $table->updated[2]['time']);
        $this->assertSame('Steve', $table->updated[2]['speaker']);
        $this->assertSame('Hi', $table->updated[2]['message']);
        $this->assertSame(mktime(12, 28, 0, 12, 18, 2012), $table->updated[4]['time']);
        $this->assertSame('Alex', $table->updated[4]['speaker']);
        $this->assertSame('Disconnected', $table->updated[4]['message']);
        $this->assertSame('', $table->updated[4]['ip']);
    }
}
