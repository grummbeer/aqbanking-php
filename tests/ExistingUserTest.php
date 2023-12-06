<?php

declare(strict_types=1);

namespace Tests;

use AqBanking\Bank;
use AqBanking\BankCode;
use AqBanking\ExistingUser;
use AqBanking\HbciVersion;
use AqBanking\User;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\ExistingUser
 * @uses \AqBanking\Bank
 * @uses \AqBanking\BankCode
 * @uses \AqBanking\HbciVersion
 * @uses \AqBanking\User
 */
class ExistingUserTest extends TestCase
{
    public function testExistingUser(): void
    {
        $bank = new Bank(
            bankCode: new BankCode('50050010'),
            hbciUrl: 'https://fints.bank.de/fints',
            hbciVersion: new HbciVersion('1.2.3'),
        );
        $userId = '1';
        $userName = 'Max Mustermann';
        $user = new User(
            userId: $userId,
            userName: $userName,
            bank: $bank,
        );

        $uniqueUserId = 5;
        $sut = new ExistingUser(
            user: $user,
            uniqueUserId: $uniqueUserId,
        );

        $this->assertSame($bank, $sut->getBank());
        $this->assertSame($userName, $sut->getUserName());
        $this->assertSame($userId, $sut->getUserId());
        $this->assertSame($uniqueUserId, $sut->getUniqueUserId());
    }
}
