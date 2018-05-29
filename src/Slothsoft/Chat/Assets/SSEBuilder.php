<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Assets;

use Slothsoft\Chat\Model;
use Slothsoft\Chat\SSEServer;
use Slothsoft\Chat\Executables\ChatExecutableCreator;
use Slothsoft\Core\DBMS\DatabaseException;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;

class SSEBuilder implements ExecutableBuilderStrategyInterface
{
    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies
    {
        $tableName = $args->get('name');
        if ($tableName === 'minecraft_log') {
            $dbName = 'cms';
        } else {
            $dbName = 'chat';
        }
        $lastId = (int) $args->get('lastId');
        
        $chat = new Model($dbName, $tableName);
        try {
            $chat->init();
        } catch (DatabaseException $e) {
        }
        
        $sse = new SSEServer($tableName, $dbName, $chat);
        try {
            $sse->init($lastId);
        } catch(DatabaseException $e) {
        }
        
        $creator = new ChatExecutableCreator($this, $args);
        return $creator->createSSE($sse);
    }

}

