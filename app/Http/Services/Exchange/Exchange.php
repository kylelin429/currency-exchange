<?php

namespace App\Http\Services\Exchange;

class Exchange
{
    /**
     * @var array
     */
    private $list;

    public function __construct(array $list)
    {
        $this->list = $list;
    }

    public function getList()
    {
        return $this->list;
    }

}
