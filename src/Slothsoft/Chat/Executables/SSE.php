<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Executables;

use Slothsoft\Chat\SSEServer;
use Slothsoft\Farah\Module\Executables\ExecutableBase;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlStreamIdentifier;
use Slothsoft\Farah\Module\Results\ResultCreator;

class SSE extends ExecutableBase
{
    private $server;
    public function __construct(SSEServer $server) {
        $this->server = $server;
    }
    protected function loadResult(FarahUrlStreamIdentifier $type)
    {
        $creator = new ResultCreator($this, $type);
        return $creator->createHttpStreamResult($this->server->getStream());
    }
}

