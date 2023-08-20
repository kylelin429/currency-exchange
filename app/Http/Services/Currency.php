<?php

namespace App\Http\Services;

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
        $this->amount = $this->parseIntegerFromString($amount);
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
     * @param string $amount Amount 可以帶$符號或是千分位符號，例如 $1,525
     * @return string
     */
    private function parseIntegerFromString($amount)
    {
        if ($this->isSymbolExistButNotAtTheStart('$', $amount)) {
            throw new \InvalidArgumentException('amount must be an valid currency');
        }

        $amount = str_replace('$', "", $amount);
        $amount = str_replace(',', "", $amount);

        if (filter_var($amount, FILTER_VALIDATE_INT) === false) {
            throw new \InvalidArgumentException('amount must be an integer');
        }

        return $amount;
    }

    /**
     * 若有帶貨幣符號(例如 $)，判斷是否在金額開頭
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
