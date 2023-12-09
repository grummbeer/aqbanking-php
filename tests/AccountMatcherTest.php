<?php

declare(strict_types=1);

namespace Tests;

use AqBanking\Account;
use AqBanking\AccountMatcher;
use AqBanking\BankCode;
use AqBanking\Command\ListAccountsCommand;
use AqBanking\ExistingAccount;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\AccountMatcher
 * @uses \AqBanking\Account
 * @uses \AqBanking\ExistingAccount
 * @uses \AqBanking\BankCode
 */
class AccountMatcherTest extends TestCase
{
    private static string $matchingAccountNumber = '100100100';

    private static string $matchingBankCode = '50050010';

    /**
     * @dataProvider samples
     */
    public function testAccountMatcher(string $bankCode, string $accountNumber, bool $pass): void
    {
        $account = new Account(
            bankCode: new BankCode($bankCode),
            accountNumber: $accountNumber,
            accountHolderName: 'Max Mustermann'
        );

        $matcher = new AccountMatcher([
            [
                ListAccountsCommand::BANK => '50050010',
                ListAccountsCommand::NUMBER => '987654123',
                ListAccountsCommand::UNIQUE_ID => '1',
            ],
            [
                ListAccountsCommand::BANK => self::$matchingBankCode,
                ListAccountsCommand::NUMBER => self::$matchingAccountNumber,
                ListAccountsCommand::UNIQUE_ID => '2',
            ],
        ]);

        if (! $pass) {
            $this->assertNull($matcher->getExistingAccount($account));
        } else {
            $this->assertInstanceOf(ExistingAccount::class, $matcher->getExistingAccount($account));
        }
    }

    public static function samples(): array
    {
        return [
            'match' => [self::$matchingBankCode, self::$matchingAccountNumber, true],
            'no match' => ['12345678', '123569', false],
        ];
    }
}
