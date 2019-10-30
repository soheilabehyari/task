<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExchangeRateRequest;
use App\Repositories\Eloquent\ExchangeRateRepository;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis as Cache;
use Laravel\Lumen\Http\ResponseFactory;


class ExchangeRateApiController extends Controller
{

    private $exchangeBase = 'EUR';
    private $repository;

    /**
     * Create a new controller instance.
     *
     * @param ExchangeRateRepository $repo
     */
    public function __construct(ExchangeRateRepository $repo)
    {
        $this->repository = $repo;
    }

    /**
     * Return exchange rate to user based on x & y currency and given date
     *
     * @param ExchangeRateRequest $request
     * @return Response|ResponseFactory
     */
    public function getRate(ExchangeRateRequest $request)
    {
        $date = $this->getRateDate($request->get('date'));
        $from = $request->get('from');
        $to = $request->get('to');

        $exRates = $this->getExchangeRate($from, $to, $date);

        if ($exRates) {
            $rate = $this->calculateRate($from, $to, $exRates, $this->exchangeBase);
            return $this->respond($request, ['rate' => $rate]);
        } else
            return $this->respond($request, [], 404);
    }

    /**
     * if requested exchange rates not exist on cash then retrieve from database
     *
     * @param $from
     * @param $to
     * @param $date
     * @return array
     */
    private function getExchangeRate($from, $to, $date)
    {
        $fromRateValue = Cache::hget($date, $from);
        $toRateValue = Cache::hget($date, $to);

        if (!$fromRateValue or !$toRateValue) {
            $rates = $this->repository->toArray($this->repository->findWhereLast('rate_date', $date));
        } else {
            $rates = [
                'rates' => [
                    $from => $fromRateValue,
                    $to => $toRateValue
                ]
            ];
        }
        return $rates;
    }

    /**
     * calculate rate from X currency to Y currency
     *
     * @param $from
     * @param $to
     * @param $exRates
     * @param $base
     * @return float
     */
    private function calculateRate($from, $to, $exRates, $base)
    {
        $fromValue = array_key_exists($from, $exRates['rates']) ? $exRates['rates'][$from] : 1;
        $toValue = array_key_exists($to, $exRates['rates']) ? $exRates['rates'][$to] : 1;
        return ($from == $base) ? $toValue : $fromValue / $toValue;
    }

    /**
     * Set api date to current date if date not provided by user
     *
     * @param $date
     * @return string
     */
    private function getRateDate($date)
    {
        return !($date)? Carbon::today()->format('Y-m-d') : $date;
    }
}
