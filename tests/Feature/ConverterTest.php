<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Services\Currency\Currency;

class ConverterTest extends TestCase
{
    public function successProvider()
    {
        return [
            ['$1,525', 'USD', 'JPY', 111.801, '170496.52500000000000'],
            ['$12,479', 'USD', 'USD', 1, '12479.00000000000000'],
            ['$123,091', 'USD', 'TWD', 30.444, '3747382.40400000000000'],
        ];
    }

    /**
     * @dataProvider successProvider
     */
    public function test_conversion($sourceAmt, $sourceCode, $targetCode, $ratio, $result)
    {
        $currency = new Currency($sourceCode, $sourceAmt);
        $converter = \Mockery::mock('\App\Http\Services\Currency\CurrencyConverter')->makePartial();
        $converter->shouldReceive('getConversionRatio')
            ->once()
            ->with($currency->getCode(), $targetCode)
            ->andReturn($ratio);

        $resultCurrency = $converter->convert($currency, $targetCode);

        $this->assertInstanceOf(Currency::class, $resultCurrency);
        $this->assertEquals($result, $resultCurrency->getAmount());
        $this->assertEquals($targetCode, $resultCurrency->getCode());
    }
}
