<?php

use App\Services\ExchangeRateProxy;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class ExchangeRateProxyTest extends TestCase
{
    private $proxy;
    protected $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockHandler = new MockHandler();

        $httpClient = new Client([
            'handler' => $this->mockHandler,
        ]);

        $this->proxy = new ExchangeRateProxy($httpClient);
    }

    protected function tearDown(): void
    {
        $this->proxy = null;
    }

    public function testExchangeService()
    {
        $body = file_get_contents(__DIR__ . '/fixtures/exchange.json');
        $this->mockHandler->append(new Response(200, [], $body));

        $response = $this->proxy->getLatestRates();
        $this->assertEquals(json_decode($body, true), $response);
    }
}
