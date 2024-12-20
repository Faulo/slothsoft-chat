<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Assets;

use Slothsoft\Chat\Model;
use Slothsoft\Chat\SSEServer;
use Slothsoft\Core\DBMS\DatabaseException;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\SSE\Results\ServerResultBuilder;

class PullBuilder implements ExecutableBuilderStrategyInterface {

    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
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
        } catch (DatabaseException $e) {}

        $sse = new SSEServer($tableName, $dbName, $chat);
        try {
            $sse->init($lastId);
        } catch (DatabaseException $e) {}

        $resultBuilder = new ServerResultBuilder($sse);
        return new ExecutableStrategies($resultBuilder);
    }
}

