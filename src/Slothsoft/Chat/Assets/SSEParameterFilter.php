<?php
namespace Slothsoft\Chat\Assets;

use Slothsoft\Farah\Module\Asset\ParameterFilterStrategy\AbstractMapParameterFilter;

class SSEParameterFilter extends AbstractMapParameterFilter
{
    protected function loadMap(): array
    {
        return [
            'mode' => '',
            'name' => 'minecraft_log',
            'lastId' => 0,
        ];
    }
}

