<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Assets;

use Slothsoft\Chat\Executables\ChatExecutableCreator;
use Slothsoft\Farah\Module\Executables\ExecutableInterface;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Node\Asset\AssetBase;
use Slothsoft\Farah\Module\ParameterFilters\MapFilter;
use Slothsoft\Farah\Module\ParameterFilters\ParameterFilterInterface;

class Fetch extends AssetBase
{
    protected function loadParameterFilter(): ParameterFilterInterface
    {
        return new MapFilter([
            'chat-database' => 'minecraft_log',
            'chat-duration' => 1,
        ]);
    }

    protected function loadExecutable(FarahUrlArguments $args): ExecutableInterface
    {
        $tableName = $args->get('chat-database');
        if ($tableName === 'minecraft_log') {
            $dbName = 'cms';
        } else {
            $dbName = 'chat';
        }
        $duration = (int) $args->get('chat-duration');
        
        $creator = new ChatExecutableCreator($this, $args);
        return $creator->createFetch($dbName, $tableName, $duration);
    }
}

