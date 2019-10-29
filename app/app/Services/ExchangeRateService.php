<?php


namespace App\Services;


use App\Repositories\Eloquent\ExchangeRateRepository;
use Ivory\Serializer\Serializer;

class ExchangeRateService
{

    private $proxy;
    private $repository;
    private $serializer;
    private $exchangeRates;
    private $queue;

    public function __construct(ExchangeRateProxy $exchangeRateProxy, ExchangeRateRepository $repository, Serializer $serializer, QueueService $queue)
    {
        $this->proxy = $exchangeRateProxy;
        $this->repository = $repository;
        $this->serializer = $serializer;
        $this->queue = $queue;
    }

    public function run()
    {
        $this->setExchangeRates();
        $result = $this->saveExchangeRates();
        if ($result->wasRecentlyCreated) {
            $this->notifyServices();
        }
    }

    private function setExchangeRates()
    {
        $this->exchangeRates = $this->proxy->getLatestRates();
    }

    private function saveExchangeRates()
    {
        $record = $this->repository->firstOrCreate(
            [
                'rate_date' => $this->exchangeRates['date'],
                'rates->USD' => $this->exchangeRates['rates']['USD']
            ],
            $this->format($this->exchangeRates)
        );
        return $record;
    }

    private function notifyServices()
    {
        $this->queue->publish($this->exchangeRates['rates']['USD'], env('QUEUE_EXCHANGE_QUEUE'));
    }

    private function format($response)
    {
        if ($response) {
            return [
                'base' => $response['base'],
                'rate_date' => $response['date'],
                'rates' => json_encode($response['rates']),
            ];
        }
        return [];
    }
}
