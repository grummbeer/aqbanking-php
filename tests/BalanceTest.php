<?php

declare(strict_types=1);

namespace Tests;

use AqBanking\Balance;
use DateTime;
use DateTimeInterface;
use Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\Balance
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
                'currency' => 'EUR',
                'priceUnit' => 100,
            ],
            'date' => $date->format('Y-m-d'),
        ], $sut->toArray());
    }
}
