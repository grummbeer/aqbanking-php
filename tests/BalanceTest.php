<?php

use AqBanking\Balance;
use Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * @covers Balance
 */
class BalanceTest extends TestCase
{
    public function testBalance(): void
    {
        $date = new DateTime();
        $sut = new Balance(
            date: $date,
            value: Money::EUR(10),
            type: 'noted',
        );

        $this->assertInstanceOf(DateTimeInterface::class, $sut->getDate());
        $this->assertInstanceOf(Money::class, $sut->getValue());
        $this->assertSame('noted', $sut->getType());
        $this->assertSame([
            'type' => 'noted',
            'value' => [
                'amount' => '10',
                'priceUnit' => 100,
                'currency' => 'EUR',
            ],
            'date' => $date->format('Y-m-d'),
        ], $sut->toArray());
    }
}