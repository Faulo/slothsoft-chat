<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Assets;

use Slothsoft\Chat\Executables\ChatExecutableCreator;
use Slothsoft\Farah\Module\Executables\ExecutableInterface;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Node\Asset\AssetBase;

class ServerSentEvent extends AssetBase
{
    protected function loadExecutable(FarahUrlArguments $args): ExecutableInterface
    {
        $creator = new ChatExecutableCreator($this, $args);
        return $creator->createNullExecutable();
    }
}

