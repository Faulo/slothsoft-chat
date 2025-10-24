<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\API\JavaScript;

use PHPUnit\Framework\Constraint\IsEqual;
use Slothsoft\FarahTesting\FarahServerTestCase;

final class ShoutboxTest extends FarahServerTestCase {
    
    protected static function setUpServer(): void {}
    
    protected function setUpClient(): void {
        $this->client->request('GET', '/slothsoft@farah/example-page');
    }
    
    public function test_Shoutbox(): void {
        $arguments = [];
        
        $actual = $this->client->executeAsyncScript(<<<EOT
async function test() {
    document.body.innerHTML = '<form data-chat-id="form" data-chat-last-id="0" data-chat-database="test"><ul data-chat-id="list" /><input data-chat-id="input" disabled="disabled" /></form>';

    await import("/slothsoft@chat/js/Shoutbox");

    await new Promise(resolve => window.setTimeout(resolve, 100));

    return document.querySelector("input").disabled;
}

import("/slothsoft@farah/js/Test").then(Test => Test.run(test, arguments));
EOT, $arguments);
        
        $this->assertThat($actual, new IsEqual(false));
    }
}