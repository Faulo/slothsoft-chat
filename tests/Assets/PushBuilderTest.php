<?php
declare(strict_types = 1);

namespace Slothsoft\Chat\Assets;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Slothsoft\Core\DBMS\Client;
use Slothsoft\Farah\FarahUrl\FarahUrl;
use Slothsoft\Farah\Kernel;
use Slothsoft\Farah\RequestStrategy\LookupAssetStrategy;

/**
 * PushBuilderTest
 *
 * @see PushBuilder
 *
 */
final class PushBuilderTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(PushBuilder::class), "Failed to load class 'Slothsoft\Chat\Assets\PushBuilder'!");
    }
    
    /** @runInSeparateProcess */
    public function testPushConsumesTheMessageAndReturnsNoContent(): void {
        putenv(Client::ENV_CONNECTION_PASSWORD);
        putenv(Client::ENV_CONNECTION_PASSWORD_FILE);
        Client::clearDefaultAuthority();
        
        $url = FarahUrl::createFromReference('farah://slothsoft@chat/data/sse/push');
        $request = (new ServerRequest('POST', $url, ['Content-Type' => 'application/json'], '"Hello"', '1.1', [
            'REQUEST_TIME' => 123
        ]))->withQueryParams([
            'name' => 'push_test',
            'type' => 'message'
        ]);
        Kernel::setCurrentRequest($request);
        
        $response = (new LookupAssetStrategy())->process($request);
        
        $this->assertSame(204, $response->getStatusCode());
        $this->assertSame('', (string) $response->getBody());
        $this->assertTrue($request->getBody()->eof());
    }
}
