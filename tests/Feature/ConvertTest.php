<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Services\Currency\Currency;

class ConvertTest extends TestCase
{
    public function test_conversion()
    {
        $currency = new Currency('USD', '$1,525');
        $converter = \Mockery::mock('\App\Http\Services\Currency\CurrencyConverter')->makePartial();
        $converter->shouldReceive('getConversionRatio')
            ->once()
            ->with($currency->getCode(), 'JPY')
            ->andReturn(111.801);

        $resultCurrency = $converter->convert($currency, 'JPY');

        $this->assertInstanceOf(Currency::class, $resultCurrency);
        $this->assertEquals('170496.52500000000000', $resultCurrency->getAmount());
        $this->assertEquals('JPY', $resultCurrency->getCode());
    }
}
