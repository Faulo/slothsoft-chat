<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Assets;

use Slothsoft\Farah\Module\Asset\ParameterFilterStrategy\AbstractMapParameterFilter;

class FetchParameterFilter extends AbstractMapParameterFilter
{
    protected function loadMap(): array
    {
        return [
            'chat-database' => 'minecraft_log',
            'chat-duration' => 1,
        ];
    }
}

