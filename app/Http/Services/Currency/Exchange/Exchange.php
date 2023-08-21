<?php

namespace App\Http\Services\Currency\Exchange;

interface Exchange
{
    /**
     * 取得匯率兌換表
     *
     * @return array|null
     */
    public function getList();
}
