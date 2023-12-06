<?php

declare(strict_types=1);

namespace Tests;

use AqBanking\Bank;
use AqBanking\BankCode;
use AqBanking\HbciVersion;
use AqBanking\User;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\User
 * @uses \AqBanking\Bank
 * @uses \AqBanking\BankCode
 * @uses \AqBanking\HbciVersion
 */
class UserTest extends TestCase
{
    public function testUser(): void
    {
        $bank = new Bank(
            bankCode: new BankCode('50050010'),
            hbciUrl: 'https://fints.bank.de/fints',
            hbciVersion: new HbciVersion('1.2.3'),
        );
        $userId = '1';
        $userName = 'Max Mustermann';

        $sut = new User(
            userId: $userId,
            userName: $userName,
            bank: $bank,
        );

        $this->assertSame($userId, $sut->getUserId());
        $this->assertSame($userName, $sut->getUserName());
        $this->assertSame($bank, $sut->getBank());
    }
}
