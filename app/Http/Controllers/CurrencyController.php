<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Rules\CurrencyRule;
use App\Http\Services\Currency;
use App\Traits\ErrorParser;
use App\Http\Services\Exchange\Exchange;
use App\Http\Services\CurrencyConverter;

class CurrencyController extends Controller
{
    use ErrorParser;

    const AVAILABLE_CURRENCY = ['TWD', 'JPY', 'USD'];

    public function convertCurrency(Request $request)
    {
        try {
            $request->validate([
                'source' => ['required', Rule::in(self::AVAILABLE_CURRENCY)],
                'target' => ['required', Rule::in(self::AVAILABLE_CURRENCY)],
                'amount' => ['required', new CurrencyRule]
            ]);

            $sourceCode = $request->query('source');
            $targetCode = $request->query('target');
            $amount = $request->query('amount');

            $exchange = new Exchange([
                "TWD" => [
                    "TWD" => 1,
                    "JPY" => 3.669,
                    "USD" => 0.03281
                ],
                "JPY" => [
                    "TWD" => 0.26956,
                    "JPY" => 1,
                    "USD" => 0.00885
                ],
                "USD" => [
                    "TWD" => 30.444,
                    "JPY" => 111.801,
                    "USD" => 1
                ]
            ]);
            $converter = new CurrencyConverter($exchange);

            $sourceCurrency = new Currency($sourceCode, $amount);
            $targetAmount = $converter->convert($sourceCurrency, $targetCode);

            return response()->json([
                'msg' => 'success',
                'amount' => $this->formatTargetAmount($targetAmount)
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'msg' => 'failed',
                'amount' => '',
                'debug' => $this->parseException($exception)
            ]);
        }
    }

    /**
     * 格式化輸出金額
     *
     * @param $targetAmount
     * @return string
     */
    protected function formatTargetAmount($targetAmount)
    {
        $targetAmount = round($targetAmount, 2);
        return '$' . number_format($targetAmount, 2, '.', ',');
    }

}
