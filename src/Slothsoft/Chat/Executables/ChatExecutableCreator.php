<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Executables;

use Slothsoft\Chat\Model;
use Slothsoft\Chat\SSEServer;
use Slothsoft\Farah\Module\Executables\ExecutableCreator;
use Slothsoft\Farah\Module\Executables\ExecutableInterface;

class ChatExecutableCreator extends ExecutableCreator
{
    public function createFetch(Model $chat, int $duration) : ExecutableInterface {
        return $this->initExecutable(new Fetch($chat, $duration));
    }
    public function createSSE(SSEServer $server) : ExecutableInterface {
        return $this->initExecutable(new SSE($server));
    }
}

