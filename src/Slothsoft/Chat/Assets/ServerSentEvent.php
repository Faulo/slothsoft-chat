<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Assets;

use Slothsoft\Chat\Model;
use Slothsoft\Chat\SSEServer;
use Slothsoft\Chat\Executables\ChatExecutableCreator;
use Slothsoft\Core\DBMS\DatabaseException;
use Slothsoft\Farah\Module\Executables\ExecutableInterface;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Node\Asset\AssetBase;
use Slothsoft\Farah\Module\ParameterFilters\MapFilter;
use Slothsoft\Farah\Module\ParameterFilters\ParameterFilterInterface;


class ServerSentEvent extends AssetBase
{
    protected function loadParameterFilter(): ParameterFilterInterface
    {
        return new MapFilter([
            'mode' => '',
            'name' => 'minecraft_log',
            'lastId' => 0,
        ]);
    }
    
    protected function loadExecutable(FarahUrlArguments $args): ExecutableInterface
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

