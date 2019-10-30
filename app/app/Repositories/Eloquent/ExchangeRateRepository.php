<?php

namespace App\Repositories\Eloquent;

use App\Models\ExchangeRate;


class ExchangeRateRepository extends BaseRepository
{

    public function entity()
    {
        return ExchangeRate::class;
    }

    public function serialize($data)
    {
        if ($data) {
            return [
                'base' => $data['base'],
                'rate_date' => $data['date'],
                'rates' => json_encode($data['rates']),
            ];
        }
        return [];
    }

    public function toArray($data)
    {
        if ($data) {
            return [
                'base' => $data['base'],
                'rate_date' => $data['rate_date'],
                'rates' => json_decode($data['rates'], true),
            ];
        }
        return [];
    }
}
