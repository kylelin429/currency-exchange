<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CurrencyTest extends TestCase
{
    public function successProvider()
    {
        return [
            'USD to JPY' => [['source' => 'USD', 'target' => 'JPY', 'amount' => '$1,525'], '$170,496.53'],
            'USD to USD' => [['source' => 'USD', 'target' => 'USD', 'amount' => '$12,479'], '$12,479.00'],
            'USD to TWD' => [['source' => 'USD', 'target' => 'TWD', 'amount' => '$123,091'], '$3,747,382.40'],
            'TWD to USD' => [['source' => 'TWD', 'target' => 'USD', 'amount' => '$32,026'], '$1,050.77'],
            'JPY to TWD' => [['source' => 'JPY', 'target' => 'TWD', 'amount' => '$532,026'], '$143,412.93'],
            'Amount Without Dollar Sign' => [['source' => 'JPY', 'target' => 'TWD', 'amount' => '532,026'], '$143,412.93'],
            'Large Amount' => [['source' => 'JPY', 'target' => 'TWD', 'amount' => '1,532,026,985'], '$412,973,194.08'],
            'Float Amount' => [['source' => 'USD', 'target' => 'TWD', 'amount' => '985.05'], '$29,988.86'],
            'Negative Amount' => [['source' => 'TWD', 'target' => 'JPY', 'amount' => '-1525'], '$-5,595.23'],
        ];
    }

    public function failedProvider()
    {
        return [
            'Invalid Source' => [['source' => 'AAA', 'target' => 'JPY', 'amount' => '$1,525']],
            'Invalid Amount With Non-numeric Char' => [['source' => 'TWD', 'target' => 'JPY', 'amount' => '$1abc525']],
            'Invalid Amount With Symbol Inside' => [['source' => 'TWD', 'target' => 'JPY', 'amount' => '1$525']],
        ];
    }

    /**
     * 測試成功
     *
     * @dataProvider successProvider
     * @return void
     */
    public function test_success($params, $amount)
    {
        $response = $this->call('get', 'api/currency/convert', $params);

        $response
            ->assertStatus(200)
            ->assertJson([
                'msg' => 'success',
                'amount' => $amount
        ]);

    }

    /**
     * 測試失敗
     *
     * @dataProvider failedProvider
     * @return void
     */
    public function test_failed($params)
    {
        $response = $this->call('get', 'api/currency/convert', $params);

        $response
            ->assertStatus(200)
            ->assertJson([
                'msg' => 'failed',
            ]);

    }
}
