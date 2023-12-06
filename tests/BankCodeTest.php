<?php

declare(strict_types=1);

namespace Tests;

use AqBanking\BankCode;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\BankCode
 */
class BankCodeTest extends TestCase
{
    public function testBankCode(): void
    {
        $bankCode = '50050010';
        $sut = new BankCode($bankCode);

        $this->assertSame($bankCode, $sut->getString());
    }
}
