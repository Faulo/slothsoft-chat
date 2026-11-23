<?php
declare(strict_types = 1);

namespace Slothsoft\Chat\Assets;

use Slothsoft\Chat\Model;
use Slothsoft\Core\Configuration\ConfigurationRequiredException;
use Slothsoft\Core\DBMS\DatabaseException;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Kernel;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\NullResultBuilder;

class PushBuilder implements ExecutableBuilderStrategyInterface {
    
    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
        $tableName = $args->get('name');
        if ($tableName === 'minecraft_log') {
            $dbName = 'cms';
        } else {
            $dbName = 'chat';
        }
        
        $chat = new Model($dbName, $tableName);
        try {
            $chat->init();
        } catch (DatabaseException $e) {
        }
        
        try {
            $request = Kernel::getCurrentRequest();
            $env = $request->getServerParams();
            
            $messageBody = json_decode($request->getBody()->getContents());
            $messageTime = (int) ($env['REQUEST_TIME'] ?? 0);
            $messageIp = (string) ($env['REMOTE_ADDR'] ?? '');
            
            $chat->insert($messageBody, $messageTime, $messageIp);
        } catch (ConfigurationRequiredException $e) {
        }
        
        $resultBuilder = new NullResultBuilder();
        return new ExecutableStrategies($resultBuilder);
    }
}
