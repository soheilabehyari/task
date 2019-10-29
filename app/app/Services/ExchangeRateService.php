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
    private $rabbit;

    public function __construct(ExchangeRateProxy $exchangeRateProxy, ExchangeRateRepository $repository, Serializer $serializer)
    {
        $this->proxy = $exchangeRateProxy;
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    public function run()
    {
        $this->setExchangeRates();
        $this->saveExchangeRates();

        $this->repository->create($this->format($this->exchangeRates));
    }

    public function setExchangeRates()
    {
        $this->exchangeRates = $this->proxy->getLatestRates();
    }

    public function saveExchangeRates()
    {
        $record = $this->repository->firstOrCreate(
            [
                'rate_date' => $this->exchangeRates['date'],
                'rates->USD' => $this->exchangeRates['rates']['USD']
            ],
            $this->format($this->exchangeRates)
        );
        $wasCreated = $record->wasRecentlyCreated;
        if ($wasCreated) {
            // we have new rate for USD, inform services via rabbit
            /* TODO: rabbit service */
            echo 'new rate';
        }
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
