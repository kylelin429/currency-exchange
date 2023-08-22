<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Services\Currency\Currency;
use App\Http\Services\Currency\CurrencyConverter;
use Mockery;

class ConverterTest extends TestCase
{
    public function conversionExample()
    {
        return [
            ['$1,525', 'USD', 'JPY', 111.801, '170496.52500000000000'],
            ['$12,479', 'USD', 'USD', 1, '12479.00000000000000'],
            ['$123,091', 'USD', 'TWD', 30.444, '3747382.40400000000000'],
        ];
    }

    public function getRatioExample()
    {
        return [
            ['USD', 'JPY', 111.801],
            ['USD', 'USD', 1],
            ['USD', 'TWD', 30.444],
            ['TWD', 'JPY', 3.669],
            ['TWD', 'USD', 0.03281],
            ['TWD', 'TWD', 1],
        ];
    }

    /**
     * @dataProvider conversionExample
     */
    public function test_conversion($sourceAmt, $sourceCode, $targetCode, $ratio, $result)
    {
        $currency = new Currency($sourceCode, $sourceAmt);
        $converter = Mockery::mock('\App\Http\Services\Currency\CurrencyConverter')->makePartial();
        $converter->shouldReceive('getConversionRatio')
            ->once()
            ->with($currency->getCode(), $targetCode)
            ->andReturn($ratio);

        $resultCurrency = $converter->convert($currency, $targetCode);

        $this->assertInstanceOf(Currency::class, $resultCurrency);
        $this->assertEquals($result, $resultCurrency->getAmount());
        $this->assertEquals($targetCode, $resultCurrency->getCode());
    }

    /**
     * @dataProvider getRatioExample
     */
    public function test_get_conversion_ratio($sourceCode, $targetCode, $result)
    {
        $exchangeList = [
            "TWD" => ["TWD" => 1, "JPY" => 3.669, "USD" => 0.03281],
            "JPY" => ["TWD" => 0.26956, "JPY" => 1, "USD" => 0.00885],
            "USD" => ["TWD" => 30.444, "JPY" => 111.801, "USD" => 1]
        ];
        $exchange = Mockery::mock('JsonExchange');
        $exchange->shouldReceive('getList')
            ->once()
            ->andReturn($exchangeList);

        $converter = new CurrencyConverter();
        $ratio = $converter->setExchange($exchange)
                ->getConversionRatio($sourceCode, $targetCode);

        $this->assertEquals($result, $ratio);
    }
}
