<?php


namespace App\Services;


use App\Repositories\Eloquent\ExchangeRateRepository;

class ExchangeRateService
{

    private $proxy;
    private $repository;
    private $exchangeRates;
    private $queue;

    public function __construct(ExchangeRateProxy $exchangeRateProxy, ExchangeRateRepository $repository, QueueService $queue)
    {
        $this->proxy = $exchangeRateProxy;
        $this->repository = $repository;
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
        $criteria = [
            'rate_date' => $this->exchangeRates['date'],
            'rates->USD' => $this->exchangeRates['rates']['USD']
        ];
        $formattedData = $this->repository->serialize($this->exchangeRates);
        $record = $this->repository->firstOrCreate($criteria, $formattedData);
        return $record;
    }

    private function notifyServices()
    {
        $this->queue->publish($this->exchangeRates['rates']['USD'], env('QUEUE_EXCHANGE_QUEUE'));
    }
}
