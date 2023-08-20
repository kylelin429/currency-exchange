<?php

namespace App\Http\Services\Currency;

class CurrencyFormatter
{
    /**
     * 將金額格式化成貨幣顯示格式
     *
     * @param string $amount
     * @return string
     */
    public function formatAmount($amount)
    {
        $amount = round($amount, 2);
        return '$' . number_format($amount, 2, '.', ',');
    }

}
