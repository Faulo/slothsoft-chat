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
    document.body.innerHTML = '<form data-chat-id="form" data-chat-last-id="0" data-chat-database="test"><ul data-chat-id="list" /><input data-chat-id="input" disabled="disabled" /></form><template xml:base="farah://slothsoft@chat/xsl/form-range"><xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"/></template>';

    window.EventSource = class extends EventTarget {
        constructor() {
            super();
            window.setTimeout(() => this.dispatchEvent(new Event("start")));
        }
    };

    await import("/slothsoft@chat/js/Shoutbox");

    const input = document.querySelector("input");
    await new Promise(resolve => {
        if (!input.disabled) {
            resolve();
            return;
        }

        const timeout = window.setTimeout(() => {
            observer.disconnect();
            resolve();
        }, 1000);
        const observer = new MutationObserver(() => {
            if (!input.disabled) {
                window.clearTimeout(timeout);
                observer.disconnect();
                resolve();
            }
        });
        observer.observe(input, {
            attributes: true,
            attributeFilter: ["disabled"],
        });
    });

    return input.disabled;
}

import("/slothsoft@farah/js/Test").then(Test => Test.run(test, arguments));
EOT, $arguments);
        
        $this->assertThat($actual, new IsEqual(false));
    }
}
