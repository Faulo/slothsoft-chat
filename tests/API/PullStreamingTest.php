<?php
declare(strict_types = 1);
namespace Slothsoft\Chat\API;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Slothsoft\Farah\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Module;
use Slothsoft\Farah\RequestStrategy\LookupAssetStrategy;

final class PullStreamingTest extends TestCase {
    
    /**
     * @runInSeparateProcess
     */
    public function testPullResponsePreservesStreamingAndStartsImmediately(): void {
        $url = FarahUrl::createFromReference('farah://slothsoft@chat/data/sse/pull?name=slothsoft&lastId=');
        $result = Module::resolveToResult($url);
        $this->assertFalse($result->lookupIsBufferable());
        $this->assertSame('text/event-stream', $result->lookupMimeType());
        
        $request = (new ServerRequest('GET', $url))->withQueryParams([
            'name' => 'slothsoft',
            'lastId' => ''
        ]);
        $response = (new LookupAssetStrategy())->process($request);
        $body = $response->getBody();
        try {
            $this->assertSame(200, $response->getStatusCode());
            $this->assertSame('text/event-stream; charset=UTF-8', $response->getHeaderLine('Content-Type'));
            $this->assertSame('no', $response->getHeaderLine('X-Accel-Buffering'));
            $this->assertSame("event:start\ndata:\n\n", $body->read(1024));
            $this->assertFalse($body->eof());
        } finally {
            $body->close();
        }
    }
}
