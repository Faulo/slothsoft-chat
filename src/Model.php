<?php
declare(strict_types = 1);
/**
 * *********************************************************************
 * Slothsoft\Chat\Model v1.01 26.10.2017 © Daniel Schulz
 *
 * Changelog:
 * v1.01 26.10.2017
 * refactoring
 * v1.00 04.11.2012
 * initial release
 * *********************************************************************
 */

namespace Slothsoft\Chat;

use DOMDocument;
use DOMDocumentFragment;
use DOMElement;
use Exception;
use RuntimeException;
use Slothsoft\Chat\MinecraftLog as Log;
use Slothsoft\Core\Calendar\DateTimeFormatter;
use Slothsoft\Core\DBMS\Manager;
use Slothsoft\Core\DBMS\Table;
use Slothsoft\Core\RCon;
use Stringable;

class Model {
    
    const NS_HTML = 'http://www.w3.org/1999/xhtml';
    
    public int $waitStep = 1;
    
    public int $waitTime = 300;
    
    public int $sendMax = 1024;
    
    protected ?Table $dbmsTable = null;
    
    protected string $dbName;
    
    protected string $dbTable;
    
    protected string $dateDisplay;
    
    protected string $dateSystem;
    
    /** @var array<string, string> */
    protected array $colorCache = [];
    
    protected bool $isMinecraft;
    
    public function __construct(string $dbName, string $dbTable) {
        $this->dateDisplay = DateTimeFormatter::FORMAT_DATETIME;
        $this->dateSystem = DateTimeFormatter::FORMAT_ATOM;
        
        $this->dbName = $dbName;
        $this->dbTable = $dbTable;
        $this->isMinecraft = $dbTable === 'minecraft_log';
    }
    
    public function init(): void {
        try {
            $this->dbmsTable = Manager::getTable($this->dbName, $this->dbTable);
            if (! $this->dbmsTable->tableExists()) {
                $this->install();
            }
        } catch (Exception $e) {
            $this->dbmsTable = null;
            throw $e;
        }
    }
    
    public function insert(string $message, int $time, string $ip): void {
        if ($this->dbmsTable) {
            if (strlen($message) and $time and strlen($ip)) {
                if ($this->isMinecraft) {
                    $rconConfig = [];
                    $missing = [];
                    foreach (['MINECRAFT_RCON_ADDRESS', 'MINECRAFT_RCON_PORT', 'MINECRAFT_RCON_PASSWORD'] as $name) {
                        $value = getenv($name);
                        if ($value === false || $value === '') {
                            $missing[] = $name;
                        }
                        $rconConfig[$name] = $value === false ? '' : $value;
                    }
                    if ($missing) {
                        throw new RuntimeException('Missing Minecraft RCON environment variables: ' . implode(', ', $missing));
                    }
                    try {
                        $rcon = new RCon($rconConfig['MINECRAFT_RCON_ADDRESS'], (int) $rconConfig['MINECRAFT_RCON_PORT'], $rconConfig['MINECRAFT_RCON_PASSWORD']);
                        $rcon->execute('say ' . $message);
                    } catch (Exception) {
                    }
                }
                $this->storeMessage([
                    'time' => $time,
                    'message' => $message,
                    'ip' => $ip,
                    'type' => Log::$messageTypes['rcon']
                ]);
            }
        }
    }
    
    /** @param array{time: int, message: string, ip: string, type: int} $message */
    protected function storeMessage(array $message): void {
        $this->dbmsTable?->insert($message);
    }
    
    /** @return array<array<array-key, mixed>> */
    public function getMessageList(int $lastId): array {
        return $this->selectMessageRows(sprintf('type IN (%s) AND id > %d', $this->getMessageTypes(), $lastId), 'ORDER BY id') ?? [];
    }
    
    private function getMessageTypes(): string {
        $messageTypes = isset($_REQUEST['chat-all']) ? Log::$messageTypes : [
            Log::$messageTypes['chat'],
            Log::$messageTypes['god'],
            Log::$messageTypes['rcon']
        ];
        return implode(',', $messageTypes);
    }
    
    public function wait(int $start): bool {
        if ($this->dbmsTable) {
            $query = sprintf('type IN (%s) AND time > %d', $this->getMessageTypes(), $start);
            for ($i = 0; $i < $this->waitTime; $i += $this->waitStep) {
                if (connection_aborted()) {
                    break;
                }
                if ($this->dbmsTable->select('id', $query)) {
                    return true;
                }
                sleep($this->waitStep);
            }
        }
        return false;
    }
    
    public function getFirstTime(): ?int {
        return $this->dbmsTable === null ? null : ($this->dbmsTable->select('time', 'time > 0 ORDER BY time ASC LIMIT 1')[0] ?? 0);
    }
    
    public function getLastTime(): ?int {
        return $this->dbmsTable === null ? null : ($this->dbmsTable->select('time', '1 ORDER BY time DESC LIMIT 1')[0] ?? 0);
    }
    
    /** @param array<array<array-key, mixed>> $messageList */
    public function createRangeDocument(array $messageList): DOMDocument {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->appendChild($this->createRangeNode($doc, $messageList));
        return $doc;
    }
    
    /** @param array<array<array-key, mixed>> $messageList */
    public function createRangeNode(DOMDocument $dataDoc, array $messageList): DOMElement {
        $retNode = $dataDoc->createElement('range');
        $retNode->setAttribute('db-name', $this->dbName);
        $retNode->setAttribute('db-table', $this->dbTable);
        foreach ($messageList as $arr) {
            $msg = (string) ($arr['message'] ?? '');
            $speaker = (string) ($arr['speaker'] ?? '');
            if ($speaker) {
                $msg = '<' . $speaker . '> ' . $msg;
            }
            $ip = (string) ($arr['ip'] ?? '');
            $color = $ip ?: $speaker;
            $time = (int) $arr['time'];
            $send = [
                'time-string' => date($this->dateDisplay, $time),
                'time-utc' => date($this->dateSystem, $time),
                'color' => $this->calcColor($color),
                'client' => $ip
            ];
            
            $node = $dataDoc->createElement('p');
            foreach ($send as $key => $val) {
                $node->setAttribute($key, $val);
            }
            $node->appendChild($this->parseText($msg, $dataDoc));
            $retNode->appendChild($node);
            $retNode->setAttribute('last-time', (string) $time);
            $retNode->setAttribute('last-id', (string) $arr['id']);
        }
        return $retNode;
    }
    
    public function getRangeNode(int $start, int $end, DOMDocument $dataDoc): DOMElement {
        $messageList = $this->selectMessageRows(sprintf('type IN (%s) AND time > %d AND time <= %d', $this->getMessageTypes(), $start, $end), 'ORDER BY time ASC');
        return $this->createRangeNode($dataDoc, $messageList ?? []);
    }
    
    /** @return array<array<array-key, mixed>>|null */
    protected function selectMessageRows(string $query, string $suffix): ?array {
        return $this->dbmsTable?->select(true, $query, $suffix);
    }
    
    protected function parseText(string $text, DOMDocument $doc): DOMDocumentFragment {
        $retFragment = $doc->createDocumentFragment();
        $match = [];
        while (preg_match('/(^.*?)(https?:\/\/[^ ]+)(.*$)/i', $text, $match)) {
            if (strlen($match[1])) {
                $retFragment->appendChild($this->createText($match[1], $doc));
            }
            $aNode = $doc->createElementNS(self::NS_HTML, 'a');
            $aNode->setAttribute('href', $match[2]);
            $aNode->setAttribute('rel', 'external nofollow');
            $tmp = explode('/', $match[2]);
            do {
                $linkName = array_pop($tmp);
            } while (! strlen($linkName) and count($tmp));
            $aNode->appendChild($this->createText($linkName, $doc));
            $retFragment->appendChild($aNode);
            $text = $match[3];
        }
        $retFragment->appendChild($this->createText($text, $doc));
        return $retFragment;
    }
    
    /** @var list<string> */
    protected array $htmlTags = [
        'u',
        'i',
        'b'
    ];
    
    protected function createText(string $text, DOMDocument $doc): DOMDocumentFragment {
        $retFragment = $doc->createDocumentFragment();
        foreach ($this->htmlTags as $htmlTag) {
            $expr = sprintf('/(^.*?)\<%1$s\>(.+?)\<\/%1$s\>(.*$)/i', $htmlTag);
            $match = [];
            while (preg_match($expr, $text, $match)) {
                if (strlen($match[1])) {
                    $retFragment->appendChild($this->createText($match[1], $doc));
                }
                $node = $doc->createElementNS(self::NS_HTML, $htmlTag);
                $node->appendChild($this->createText($match[2], $doc));
                $retFragment->appendChild($node);
                $text = $match[3];
            }
        }
        $retFragment->appendChild($doc->createTextNode($text));
        return $retFragment;
    }
    
    protected function calcColor(string $color): string {
        if (! isset($this->colorCache[$color])) {
            $rgb = [
                255,
                255,
                255
            ];
            $md5 = md5($color);
            for ($i = 0; $i < 12; $i += 2) {
                $key = (int) ($i / 4);
                $rgb[$key] = (int) min($rgb[$key], 2 / 3 * hexdec(substr($md5, $i, 2)));
            }
            $this->colorCache[$color] = implode(',', $rgb);
        }
        return $this->colorCache[$color];
    }
    
    protected function install(): void {
        $sqlCols = [
            'id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'time' => 'int(11) NOT NULL',
            'message' => 'text NOT NULL',
            'speaker' => 'tinytext NULL',
            'type' => 'int(11) NOT NULL DEFAULT "0"',
            'ip' => 'tinytext NOT NULL',
            'raw' => 'text NULL'
        ];
        $sqlKeys = [
            'id',
            'time',
            'type'
        ];
        $this->dbmsTable?->createTable($sqlCols, $sqlKeys);
    }
}
