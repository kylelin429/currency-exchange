<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\CurrencyService;
use Illuminate\Validation\Rule;
use App\Rules\Currency;
use App\Traits\ErrorParser;

class CurrencyController extends Controller
{
    use ErrorParser;

    const AVAILABLE_CURRENCY = ['TWD', 'JPY', 'USD'];

    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function convertCurrency(Request $request)
    {
        try {
            $source = $request->query('source');
            $target = $request->query('target');
            $amount = $request->query('amount');

            $request->validate([
                'source' => ['required', Rule::in(self::AVAILABLE_CURRENCY)],
                'target' => ['required', Rule::in(self::AVAILABLE_CURRENCY)],
                'amount' => ['required', new Currency]
            ]);

            $targetAmount = $this->currencyService->exchangeCurrency($source, $target, $this->parseAmountToInteger($amount));

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
     * 解析輸入金額數字的部份
     *
     * @param $amount
     * @return float
     */
    protected function parseAmountToInteger($amount)
    {
        $amount = preg_replace("/[^0-9.\-]/", null, $amount);
        return floatval($amount);
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
