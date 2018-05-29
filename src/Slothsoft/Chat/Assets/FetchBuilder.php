<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Assets;

use Slothsoft\Chat\Model;
use Slothsoft\Core\DBMS\DatabaseException;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\DOMWriterResultBuilder;

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
        $duration = (int) $args->get('chat-duration');
        
        $chat = new Model($dbName, $tableName);
        try {
            $chat->init();
        } catch (DatabaseException $e) {
        }
        
        $writer = new Fetch($chat, $duration);
        $resultBuilder = new DOMWriterResultBuilder($writer);
        return new ExecutableStrategies($resultBuilder);
    }

}

