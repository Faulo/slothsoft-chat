<?php
declare(strict_types = 1);

namespace Slothsoft\Chat;

use Exception;
use Slothsoft\Core\Calendar\DateTimeFormatter;
use Slothsoft\Core\DBMS\Manager;
use Slothsoft\Core\DBMS\Table;

/*
 * 2010-12-06 21:36:52 [INFO] Stopping server
 * 2010-12-06 21:36:52 [INFO] Saving chunks
 * 2010-12-06 21:37:22 [INFO] Starting minecraft server version 0.2.8
 * 2010-12-06 21:37:22 [INFO] Loading properties
 * 2010-12-06 21:37:22 [INFO] Starting Minecraft server on *:25565
 * 2010-12-06 21:37:22 [WARNING] **** SERVER IS RUNNING IN OFFLINE/INSECURE MODE!
 * 2012-12-18 12:27:25 [SEVERE] Encountered an unexpected exception t
 */

class MinecraftLog {
    
    const WATCH_START = '[%s] Starting MinecraftLog watching for file "%s"~ /o/%s';
    
    const WATCH_LINE = '[%s] %s%s';
    
    const WATCH_ERROR = '[%s] ERROR! /o\\%s%s';
    
    /** @var array<string, int> */
    public static array $messageTypes = [
        'ignore' => -5,
        'invalid' => -4,
        'unknown' => -3,
        'error' => -2,
        'warning' => -1,
        'raw' => 0,
        'system' => 1,
        'chat' => 2,
        'login' => 3,
        'logout' => 4,
        'god' => 5,
        'whisper' => 6,
        'boot' => 7,
        'shutdown' => 8,
        'rcon' => 9
    ];
    
    protected string $dbName;
    
    protected string $dbTable;
    
    protected Table $dbmsTable;
    
    public function __construct() {
    }
    
    public function init(string $dbName, string $dbTable): void {
        $this->dbName = $dbName;
        $this->dbTable = $dbTable;
        
        $this->dbmsTable = Manager::getTable($this->dbName, $this->dbTable);
    }
    
    public function watch(string $logFile, int $logInterval): void {
        echo sprintf(self::WATCH_START, date(DateTimeFormatter::FORMAT_DATETIME), $logFile, PHP_EOL);
        try {
            while (sleep($logInterval) === 0) {
                if (file_exists($logFile) and $rawData = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) {
                    file_put_contents($logFile, '');
                    foreach ($rawData as $line) {
                        $line = trim($line);
                        echo sprintf(self::WATCH_LINE, date(DateTimeFormatter::FORMAT_DATETIME), $line, PHP_EOL);
                        $this->dbmsTable->insert([
                            'raw' => utf8_encode($line)
                        ]);
                    }
                    $this->parse();
                    clearstatcache();
                }
            }
        } catch (Exception $e) {
            echo sprintf(self::WATCH_ERROR, date(DateTimeFormatter::FORMAT_DATETIME), PHP_EOL, $e->getMessage());
        }
    }
    
    public function reset(): ?int {
        return $this->dbmsTable->update([
            'type' => self::$messageTypes['raw']
        ]);
    }
    
    /** @return array<int, int> */
    public function parse(): array {
        $ret = [];
        if ($unparsed = $this->selectRawLines()) {
            foreach ($unparsed as &$line) {
                $line['type'] = self::$messageTypes['invalid'];
                $time = null;
                $match = [];
                if (preg_match('~(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2}) \[(\w+?)] (.+)~', $line['raw'], $match)) {
                    $time = mktime((int) $match[4], (int) $match[5], (int) $match[6], (int) $match[2], (int) $match[3], (int) $match[1]);
                    $msgType = $match[7];
                    $msgBody = $match[8];
                }
                if (preg_match('~\[(\d{2}):(\d{2}):(\d{2})] \[[^/]+/(\w+?)]: (.+)~', $line['raw'], $match)) {
                    $time = mktime((int) $match[1], (int) $match[2], (int) $match[3], (int) date('n'), (int) date('j'), (int) date('Y'));
                    $msgType = $match[4];
                    $msgBody = $match[5];
                }
                if ($time !== null) {
                    $msg = $msgBody;
                    $player = null;
                    $type = self::$messageTypes['unknown'];
                    switch ($msgType) {
                        case 'INFO':
                            // or preg_match('/^\[(.+?)\] (.+)/u', $msg, $match)
                            if (preg_match('~^<(.+?)> (.+)~u', $msg, $match)) {
                                $player = $match[1];
                                $msg = $match[2];
                                $type = self::$messageTypes['chat'];
                                if ($player === 'Server') {
                                    $type = self::$messageTypes['god'];
                                    $player = 'GOTT';
                                }
                                if ($player === 'Rcon') {
                                    $type = self::$messageTypes['ignore'];
                                }
                            } elseif (strpos($msg, '[Server] ') === 0) {
                                $msg = substr($msg, strlen('[Server] '));
                                $player = 'GOTT';
                                $type = self::$messageTypes['god'];
                            } elseif (strpos($msg, 'Starting minecraft server version') === 0) {
                                $msg = 'Server: start (' . trim(str_replace('Starting minecraft server version', '', $msg)) . ')';
                                $type = self::$messageTypes['boot'];
                            } elseif (strpos($msg, 'Stopping server') === 0) {
                                $msg = 'Server: stop';
                                $type = self::$messageTypes['shutdown'];
                            } elseif (preg_match('/([^ \[]+).*(\d+\.\d+\.\d+\.\d+)?.*logged in/', $msg, $match)) {
                                $player = $match[1];
                                if (isset($match[2])) {
                                    $line['ip'] = $match[2];
                                }
                                $msg = null;
                                $type = self::$messageTypes['login'];
                            } elseif (preg_match('/([^ \[]+).*(\d+\.\d+\.\d+\.\d+)?.*lost connection: (.+)/', $msg, $match)) {
                                $player = $match[1];
                                $line['ip'] = $match[2];
                                $msg = $match[3];
                                $type = self::$messageTypes['logout'];
                            } elseif (preg_match('/(.+?) whispers (.+) to (.+)/', $msg, $match)) {
                                $player = $match[1];
                                $msg = '@' . $match[3] . ': ' . $match[2];
                                $type = self::$messageTypes['whisper'];
                            }
                            
                            break;
                        case 'WARNING':
                            // $msg = 'Server: stop';
                            break;
                        case 'SEVERE':
                            $type = self::$messageTypes['shutdown'];
                            break;
                    }
                    $line['time'] = $time;
                    // my_dump($time);die();
                    $line['message'] = $msg;
                    $line['speaker'] = $player;
                    $line['type'] = $type;
                }
                $id = $line['id'];
                unset($line['id']);
                if ($line['time'] > 0) {
                    $this->updateLine($line, $id);
                } else {
                    $this->deleteLine($id);
                }
                
                if (! isset($ret[$line['type']])) {
                    $ret[$line['type']] = 0;
                }
                $ret[$line['type']]++;
            }
            unset($line);
        }
        return $ret;
    }
    
    /** @return array<array<string, mixed>> */
    protected function selectRawLines(): array {
        return $this->dbmsTable->select(true, 'type = ' . self::$messageTypes['raw']) ?? [];
    }
    
    /** @param array<string, mixed> $line */
    protected function updateLine(array $line, int $id): void {
        $this->dbmsTable->update($line, $id);
    }
    
    protected function deleteLine(int $id): void {
        $this->dbmsTable->delete($id);
    }
}
