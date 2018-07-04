<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Assets;

use Slothsoft\Chat\Model;
use Slothsoft\Core\Calendar\Seconds;
use Slothsoft\Core\DBMS\DatabaseException;
use Slothsoft\Core\IO\Writable\Delegates\DOMWriterFromElementDelegate;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\DOMWriterResultBuilder;
use DOMDocument;
use DOMElement;

class FetchBuilder implements ExecutableBuilderStrategyInterface
{

    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies
    {
        $tableName = $args->get('chat-database');
        if ($tableName === 'minecraft_log') {
            $dbName = 'cms';
        } else {
            $dbName = 'chat';
        }
        $duration = Seconds::DAY * (int) $args->get('chat-duration');
        
        $end = time();
        $start = $end - $duration;
        
        $chat = new Model($dbName, $tableName);
        try {
            $chat->init();
        } catch (DatabaseException $e) {}
        
        $writer = function (DOMDocument $targetDoc) use ($chat, $start, $end): DOMElement {
            return $chat->getRangeNode($start, $end, $targetDoc);
        };
        $resultBuilder = new DOMWriterResultBuilder(new DOMWriterFromElementDelegate($writer), 'fetch.xml');
        return new ExecutableStrategies($resultBuilder);
    }
}

