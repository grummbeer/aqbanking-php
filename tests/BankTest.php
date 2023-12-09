<?php

declare(strict_types=1);

namespace Tests;

use AqBanking\Bank;
use AqBanking\BankCode;
use AqBanking\HbciVersion;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\Bank
 * @uses \AqBanking\BankCode
 * @uses \AqBanking\HbciVersion
 */
class BankTest extends TestCase
{
    public function testBank(): void
    {
        $bankCode = new BankCode('50050010');
        $hbciVersion = new HbciVersion('1.2.3');
        $hbciUrl = 'https://fints.bank.de/fints';

        $sut = new Bank(
            bankCode: $bankCode,
            hbciUrl: $hbciUrl,
            hbciVersion: $hbciVersion,
        );

        $this->assertSame($bankCode, $sut->getBankCode());
        $this->assertSame($hbciVersion, $sut->getHbciVersion());
        $this->assertSame($hbciUrl, $sut->getHbciUrl());
    }
}
