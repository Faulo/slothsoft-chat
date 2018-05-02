<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Assets;

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
        
        $creator = new ChatExecutableCreator($this, $args);
        try {
            $sse = new SSEServer($tableName, $dbName);
            $sse->init($lastId);
            return $creator->createSSE($sse);
        } catch(DatabaseException $e) {
            return $creator->createNullExecutable();
        }
    }
}

