<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Assets;

use Slothsoft\Farah\Module\Asset\ParameterFilterStrategy\AbstractMapParameterFilter;

class PushParameterFilter extends AbstractMapParameterFilter
{
    protected function loadMap(): array
    {
        return [
            'name' => 'minecraft_log',
            'type' => 'message',
        ];
    }
}

