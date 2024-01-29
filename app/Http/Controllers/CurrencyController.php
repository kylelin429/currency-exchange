<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Rules\CurrencyRule;
use App\Traits\ErrorParser;
use Illuminate\Support\Facades\Storage;
use App\Http\Services\Currency\Currency;
use App\Http\Services\Currency\CurrencyConverter;
use App\Http\Services\Currency\Exchange\JsonExchange;
use Facades\App\Http\Services\Currency\CurrencyFormatter;

class CurrencyController extends Controller
{
    use ErrorParser;

    const AVAILABLE_CURRENCY = ['TWD', 'JPY', 'USD'];

    protected $converter;

    public function __construct(CurrencyConverter $converter)
    {
        $this->converter = $converter;
    }

    public function stage1()
    {
        //test
    }

    public function stage2()
    {
        //test
    }

    public function convertCurrency(Request $request)
    {
        try {
            $request->validate([
                'source' => ['required', Rule::in(self::AVAILABLE_CURRENCY)],
                'target' => ['required', Rule::in(self::AVAILABLE_CURRENCY)],
                'amount' => ['required', new CurrencyRule]
            ]);

            // modify stage3

            $sourceCode = $request->query('source');
            $targetCode = $request->query('target');
            $amount = $request->query('amount');

            $jsonExchange = json_decode(Storage::get('exchange.json'), true)['currencies'];
            $sourceCurrency = new Currency($sourceCode, $amount);
            $targetCurrency = $this->converter->setExchange(new JsonExchange($jsonExchange))
                                              ->convert($sourceCurrency, $targetCode);

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
