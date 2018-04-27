<?php
namespace Slothsoft\Chat\Executables;

use Slothsoft\Farah\Module\Executables\ExecutableCreator;
use Slothsoft\Farah\Module\Executables\ExecutableInterface;

class ChatExecutableCreator extends ExecutableCreator
{
    public function createFetch(string $dbName, string $tableName, int $duration) : ExecutableInterface {
        return $this->initExecutable(new Fetch($dbName, $tableName, $duration));
    }
}

