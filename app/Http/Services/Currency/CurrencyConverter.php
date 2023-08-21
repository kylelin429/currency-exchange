<?php

namespace App\Http\Services\Currency;

use App\Http\Services\Currency\Exchange\JsonExchange;

class CurrencyConverter
{
    /**
     * @var JsonExchange
     */
    private $exchange;

    public function setExchange($exchange)
    {
        $this->exchange = $exchange;
        return $this;
    }

    /**
     * 匯率轉換
     *
     * @param Currency $currency
     * @param string $code
     * @return Currency
     */
    public function convert(Currency $currency, $code)
    {
        $ratio = $this->getConversionRatio($currency->getCode(), $code);
        $targetAmount = bcmul($currency->getAmount(), (string)$ratio, 14);

        return new Currency($code, $targetAmount);
    }

    /**
     * 取得兌換匯率
     *
     * @param string $sourceCode
     * @param string $targetCode
     * @return float|int
     */
    private function getConversionRatio($sourceCode, $targetCode)
    {
        $exchangeList = $this->exchange->getList();
        return $exchangeList[$sourceCode][$targetCode];
    }

}
