<?php

declare(strict_types=1);

namespace Tests;

use AqBanking\Account;
use AqBanking\BankCode;
use AqBanking\ExistingAccount;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\ExistingAccount
 * @uses \AqBanking\BankCode
 * @uses  \AqBanking\Account
 */
class ExistingAccountTest extends TestCase
{
    public function testExistingAccount(): void
    {
        $account = new Account(
            bankCode: new BankCode('50050010'),
            accountNumber: '123456',
            accountHolderName: 'Max Mustermann'
        );

        $uniqueAccountId = 5;
        $sut = new ExistingAccount(
            account: $account,
            uniqueAccountId: $uniqueAccountId,
        );

        $this->assertSame($account, $sut->getAccount());
        $this->assertSame($uniqueAccountId, $sut->getUniqueAccountId());
    }
}
