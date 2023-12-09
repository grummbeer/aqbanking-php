<?php

declare(strict_types=1);

namespace Tests;

use AqBanking\Account;
use AqBanking\BankCode;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\Account
 * @uses \AqBanking\BankCode
 */
class AccountTest extends TestCase
{
    public function testAccount(): void
    {
        $bankCode = '54850010';
        $accountNumber = '1235697890';
        $accountHolderName = 'Max Mustermann';

        $sut = new Account(
            bankCode: new BankCode($bankCode),
            accountNumber: $accountNumber,
            accountHolderName: $accountHolderName,
        );

        $this->assertInstanceOf(BankCode::class, $sut->getBankCode());
        $this->assertSame($accountNumber, $sut->getAccountNumber());
        $this->assertSame($accountHolderName, $sut->getAccountHolderName());
        $this->assertSame([
            'bankCode' => $bankCode,
            'accountNumber' => $accountNumber,
            'accountHolderName' => $accountHolderName,
        ], $sut->toArray());
    }
}
