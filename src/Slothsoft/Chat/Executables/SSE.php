<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Executables;

use Slothsoft\Chat\SSEServer;
use Slothsoft\Farah\Module\Executables\ExecutableBase;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlStreamIdentifier;
use Slothsoft\Farah\Module\Results\ResultInterface;
use Slothsoft\SSE\EventGenerator;
use Slothsoft\SSE\Results\SSEResultCreator;

class SSE extends ExecutableBase
{
    private $server;
    public function __construct(SSEServer $server) {
        $this->server = $server;
    }
    protected function loadResult(FarahUrlStreamIdentifier $type) : ResultInterface
    {
        $creator = new SSEResultCreator($this, $type);
        return $creator->createEventResult(new EventGenerator($this->server));
    }
}
