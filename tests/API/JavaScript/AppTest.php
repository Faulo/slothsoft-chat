<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\API\JavaScript;

use PHPUnit\Framework\Constraint\IsEqual;
use Slothsoft\FarahTesting\FarahServerTestCase;

final class AppTest extends FarahServerTestCase {
    
    protected static function setUpServer(): void {}
    
    protected function setUpClient(): void {
        $this->client->request('GET', '/slothsoft@farah/example-page');
    }
    
    public function test_App(): void {
        $arguments = [];
        
        $actual = $this->client->executeAsyncScript(<<<EOT
async function test() {
    const { default: SuT } = await import("/slothsoft@chat/js/Shoutbox");

    document.body.innerHTML = '<form data-chat-id="form" data-chat-last-id="0" data-chat-database="test"><ul data-chat-id="list" /><input data-chat-id="input" /></form>';

    const sut = new SuT(document.querySelector("form"));

    return document.querySelector("input").value;
}

import("/slothsoft@farah/js/Test").then(Test => Test.run(test, arguments));
EOT, $arguments);
        
        $this->assertThat($actual, new IsEqual("Initializing Server Connection..."));
    }
}