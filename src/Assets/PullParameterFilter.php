<?php
declare(strict_types = 1);

namespace Slothsoft\Chat\Assets;

use Slothsoft\Core\IO\Sanitizer\FileNameSanitizer;
use Slothsoft\Core\IO\Sanitizer\IntegerSanitizer;
use Slothsoft\Core\IO\Sanitizer\SanitizerInterface;
use Slothsoft\Farah\Module\Asset\ParameterFilterStrategy\AbstractMapParameterFilter;

class PullParameterFilter extends AbstractMapParameterFilter {
    
    /** @return array<string, SanitizerInterface> */
    protected function createValueSanitizers(): array {
        return [
            'name' => new FileNameSanitizer('minecraft_log'),
            'lastId' => new IntegerSanitizer(1)
        ];
    }
}

