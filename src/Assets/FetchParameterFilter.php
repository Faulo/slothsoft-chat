<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\Assets;

use Slothsoft\Farah\Module\Asset\ParameterFilterStrategy\AbstractMapParameterFilter;
use Slothsoft\Core\IO\Sanitizer\IntegerSanitizer;
use Slothsoft\Core\IO\Sanitizer\FileNameSanitizer;

class FetchParameterFilter extends AbstractMapParameterFilter {
    
    protected function createValueSanitizers(): array {
        return [
            'chat-database' => new FileNameSanitizer('minecraft_log'),
            'chat-duration' => new IntegerSanitizer(1)
        ];
    }
}

