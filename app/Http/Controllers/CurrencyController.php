<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Rules\CurrencyRule;
use App\Http\Services\Currency;
use App\Traits\ErrorParser;
use App\Http\Services\Exchange\JsonExchange;
use App\Http\Services\CurrencyConverter;
use Illuminate\Support\Facades\Storage;
use Facades\App\Http\Services\CurrencyFormatter;

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

            $exchangeJson = json_decode(Storage::get('exchange.json'), true)['currencies'];
            $converter = new CurrencyConverter(new JsonExchange($exchangeJson));
            $sourceCurrency = new Currency($sourceCode, $amount);
            $targetCurrency = $converter->convert($sourceCurrency, $targetCode);

            return response()->json([
                'msg' => 'success',
                'amount' => CurrencyFormatter::formatAmount($targetCurrency->getAmount())
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'msg' => 'failed',
                'amount' => '',
                'debug' => $this->parseException($exception)
            ]);
        }
    }

}
