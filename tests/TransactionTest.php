<?php

declare(strict_types=1);

namespace Tests;

use AqBanking\Account;
use AqBanking\BankCode;
use AqBanking\Transaction;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\Transaction
 * @uses \AqBanking\Account
 * @uses \AqBanking\BankCode
 */
class TransactionTest extends TestCase
{
    public function testTransaction(): void
    {
        $localAccount = new Account(new BankCode('50951469'), '12345678');
        $remoteAccount = new Account(new BankCode('50951469'), '87654321');
        $purpose = 'Some purpose';
        $valutaDate = new \DateTime('today');
        $date = new \DateTime('yesterday');
        $value = new Money(100, new Currency('EUR'));
        $type = "statement";
        $primaNota = "";
        $customerReference = "a random reference";

        $sut = new Transaction(
            $localAccount,
            $remoteAccount,
            $type,
            $purpose,
            $valutaDate,
            $date,
            $value,
            $primaNota,
            $customerReference
        );

        $this->assertEquals($localAccount, $sut->getLocalAccount());
        $this->assertEquals($remoteAccount, $sut->getRemoteAccount());
        $this->assertEquals($purpose, $sut->getPurpose());
        $this->assertEquals($valutaDate, $sut->getValutaDate());
        $this->assertEquals($date, $sut->getDate());
        $this->assertEquals($value, $sut->getValue());
    }
}
