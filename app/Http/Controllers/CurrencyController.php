<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\CurrencyService;
use Illuminate\Validation\Rule;
use App\Rules\Currency;

class CurrencyController extends Controller
{
    const AVAILABLE_CURRENCY = ['TWD', 'JPY', 'USD'];

    protected $exchangeRateService;

    public function __construct(CurrencyService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
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

            $targetAmount = $this->exchangeRateService->exchangeCurrency($source, $target, $this->parseAmountToInteger($amount));

            return response()->json([
                'msg' => 'success',
                'amount' => $this->formatTargetAmount($targetAmount)
            ]);
        } catch (\Throwable $exception) {
            $errors = $exception->getMessage();
            if (isset($exception->validator)) {
                $errors = $exception->validator->errors();
                $errors = implode(' | ', $errors->all());
            }

            return response()->json([
                'msg' => 'failed',
                'amount' => '',
                'debug' => $errors,
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
