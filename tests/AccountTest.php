<?php

use AqBanking\Account;
use AqBanking\BankCode;
use PHPUnit\Framework\TestCase;

/**
 * @covers Account
 */
class AccountTest extends TestCase
{
    public function testAccount(): void
    {
        $bankCode = '54850010';
        $accountNumber = '1235697890';
        $accountHolderName = 'Max Mustermann';
        $iban = 'DE455485001001234567890';

        $sut = new Account(
            bankCode: new BankCode($bankCode),
            accountNumber: $accountNumber,
            accountHolderName: $accountHolderName,
            iban: $iban,
        );

        $this->assertInstanceOf(BankCode::class, $sut->getBankCode());
        $this->assertSame($accountNumber, $sut->getAccountNumber());
        $this->assertSame($accountHolderName, $sut->getAccountHolderName());
        $this->assertSame($iban, $sut->getIban());
        $this->assertSame([
            'bankCode' => $bankCode,
            'accountNumber' => $accountNumber,
            'accountHolderName' => $accountHolderName,
            'iban' => $iban,
        ], $sut->toArray());
    }
}
