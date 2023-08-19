<?php

namespace App\Http\Services;

class CurrencyService
{
    /**
     * 匯率轉換
     *
     * @param string $source
     * @param string $target
     * @param float $amount
     * @return float
     */
    public function exchangeCurrency($source, $target, $amount)
    {
        return $amount * $this->getConversionRateForTarget($source, $target);
    }

    /**
     * 取得兌換匯率
     *
     * @param $source
     * @param $target
     * @return float|int
     */
    private function getConversionRateForTarget($source, $target)
    {
        $rateTable = $this->getConversionRateTable();
        return $rateTable[$source][$target];
    }

    /**
     * 取得匯率表
     *
     * @return array[]
     */
    private function getConversionRateTable()
    {
        return [
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
        ];
    }

}
