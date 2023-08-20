<?php

namespace App\Http\Services;

use App\Http\Services\Exchange\JsonExchange;

class CurrencyConverter
{
    /**
     * @var JsonExchange
     */
    private $exchange;

    public function __construct($exchange)
    {
        $this->exchange = $exchange;
    }

    /**
     * 匯率轉換
     *
     * @param Currency $currency
     * @param string $code
     * @return string
     */
    public function convert(Currency $currency, $code)
    {
        $rate = $this->getConversionRate($currency->getCode(), $code);

        return bcmul($currency->getAmount(), (string)$rate, 14);
    }

    /**
     * 取得兌換匯率
     *
     * @param string $sourceCode
     * @param string $targetCode
     * @return float|int
     */
    private function getConversionRate($sourceCode, $targetCode)
    {
        $exchangeList = $this->exchange->getList();
        return $exchangeList[$sourceCode][$targetCode];
    }

}
