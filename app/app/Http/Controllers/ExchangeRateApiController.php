<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExchangeRateRequest;
use App\Repositories\Eloquent\ExchangeRateRepository;
use Carbon\Carbon;

//use Illuminate\Http\JsonResponse;

class ExchangeRateApiController extends Controller
{

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
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function getRate(ExchangeRateRequest $request)
    {
        $date = $this->getRateDate($request->get('date'));
        $exRates = $this->getExchangeRate($date);

        if ($exRates) {
            $rate = $this->calculateRate($request->get('from'), $request->get('to'), $exRates);
            return $this->respond($request, ['rate' => $rate]);
        } else
            return $this->respond($request, [], 404);
    }

    private function getExchangeRate($date)
    {
        return $this->repository->toArray($this->repository->findWhereLast('rate_date', $date));
    }

    /**
     * calculate rate from X currency to Y currency
     *
     * @param $from
     * @param $to
     * @param $exRates
     * @return float
     */
    private function calculateRate($from, $to, $exRates)
    {
        $fromValue = array_key_exists($from, $exRates['rates']) ? $exRates['rates'][$from] : 1;
        $toValue = array_key_exists($to, $exRates['rates']) ? $exRates['rates'][$to] : 1;
        return ($from == $exRates['base']) ? $toValue : $fromValue / $toValue;
    }

    /**
     * Set api date to current date if date not provided by user
     *
     * @param $date
     * @return string
     */
    private function getRateDate($date)
    {
        if (!$date)
            $rateDate = Carbon::today()->format('Y-m-d');
        else
            $rateDate = $date;
        return $rateDate;
    }
}
