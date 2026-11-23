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
        $log = new class() extends MinecraftLog {
            /** @var array<int|string, array<string, mixed>> */
            public array $updated = [];
            
            /** @var list<int|string> */
            public array $deleted = [];
            
            protected function selectRawLines(): array {
                return [
                    ['id' => 1, 'time' => 0, 'raw' => '2012-12-18 12:27:25 [INFO] <Alex> Hello'],
                    ['id' => 2, 'time' => 0, 'raw' => '[12:27:25] [Server thread/INFO]: <Steve> Hi'],
                    ['id' => 3, 'time' => 0, 'raw' => 'invalid line'],
                    ['id' => 4, 'time' => 0, 'raw' => '2012-12-18 12:28:00 [INFO] Alex lost connection: Disconnected']
                ];
            }
            
            protected function updateLine(array $line, int|string $id): void {
                $this->updated[$id] = $line;
            }
            
            protected function deleteLine(int|string $id): void {
                $this->deleted[] = $id;
            }
        };
        $today = getdate();
        
        $counts = $log->parse();
        
        $this->assertSame([
            MinecraftLog::$messageTypes['chat'] => 2,
            MinecraftLog::$messageTypes['invalid'] => 1,
            MinecraftLog::$messageTypes['logout'] => 1
        ], $counts);
        $this->assertSame([3], $log->deleted);
        $this->assertSame([1, 2, 4], array_keys($log->updated));
        $this->assertSame(mktime(12, 27, 25, 12, 18, 2012), $log->updated[1]['time']);
        $this->assertSame('Alex', $log->updated[1]['speaker']);
        $this->assertSame('Hello', $log->updated[1]['message']);
        $this->assertSame(mktime(12, 27, 25, $today['mon'], $today['mday'], $today['year']), $log->updated[2]['time']);
        $this->assertSame('Steve', $log->updated[2]['speaker']);
        $this->assertSame('Hi', $log->updated[2]['message']);
        $this->assertSame(mktime(12, 28, 0, 12, 18, 2012), $log->updated[4]['time']);
        $this->assertSame('Alex', $log->updated[4]['speaker']);
        $this->assertSame('Disconnected', $log->updated[4]['message']);
        $this->assertSame('', $log->updated[4]['ip']);
    }
}
