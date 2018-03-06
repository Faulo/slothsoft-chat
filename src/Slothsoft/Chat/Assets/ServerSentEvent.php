<?php
namespace Slothsoft\Chat\Assets;

use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Node\Asset\AssetImplementation;
use Slothsoft\Farah\Module\Results\NullResult;
use Slothsoft\Farah\Module\Results\ResultInterface;

class ServerSentEvent extends AssetImplementation
{
    protected function loadResult(FarahUrl $url) : ResultInterface {
        return new NullResult($url);
    }
}

