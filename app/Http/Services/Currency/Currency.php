<?php

namespace App\Http\Services\Currency;

class Currency
{
    /**
     * @var string
     */
    private $amount;

    /**
     * @var string
     */
    private $code;

    public function __construct($code, $amount)
    {
        $this->amount = $this->parseNumberFromAmount($amount);
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 解析貨幣金額的數字部分
     *
     * @param string $amount Amount 可以帶$符號、千分位符號或小數點，例如 $1,525.12
     * @return string
     */
    private function parseNumberFromAmount($amount)
    {
        if ($this->isSymbolExistButNotAtTheStart('$', $amount)) {
            throw new \InvalidArgumentException('amount must be a valid currency');
        }

        $amount = str_replace('$', "", $amount);
        $amount = str_replace(',', "", $amount);

        if (filter_var($amount, FILTER_VALIDATE_INT) === false &&
            filter_var($amount, FILTER_VALIDATE_FLOAT) === false) {
            throw new \InvalidArgumentException('amount must be a valid number');
        }

        return $amount;
    }

    /**
     * 若包含貨幣符號(例如 $)，判斷是否在金額開頭
     *
     * @param string $symbol
     * @param string $amount
     * @return bool
     */
    private function isSymbolExistButNotAtTheStart($symbol, $amount)
    {
        if (strpos($amount, $symbol) !== false) {
            if (substr($amount, 0, strlen($symbol)) !== $symbol) {
                return true;
            }
        }

        return false;
    }

}
