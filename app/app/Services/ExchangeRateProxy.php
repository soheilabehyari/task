<?php


namespace App\Services;


use GuzzleHttp\Client;

class ExchangeRateProxy
{

    private $client;
    private $baseUrl;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->baseUrl = env('EXCHANGE_RATE_BASE_URL');
    }

    public function getLatestRates()
    {
        return $this->endpointRequest('/latest');
    }

    public function endpointRequest($uri)
    {
        try {
            $url = $this->baseUrl . $uri;
            $response = $this->client->get($url);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
