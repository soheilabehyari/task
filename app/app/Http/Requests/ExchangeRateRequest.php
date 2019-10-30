<?php
namespace App\Http\Requests;

use App\Rules\CurrencyRule;
use App\Rules\UppercaseRule;
use Pearl\RequestValidate\RequestAbstract;

class ExchangeRateRequest extends RequestAbstract
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $currencyRule = new CurrencyRule;
        $uppercaseRule = new UppercaseRule;
        return [
            'date' => 'date|date_format:Y-m-d',
            'from' => ['required', $currencyRule, $uppercaseRule],
            'to' => ['required', $currencyRule, $uppercaseRule],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages() : array
    {
        return [
            'from' => "from currency is required",
            'to' => "to currency is required",
        ];
    }
}
