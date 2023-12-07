<?php

declare(strict_types=1);

namespace Tests\Command;

use AqBanking\Account;
use AqBanking\BankCode;
use AqBanking\Command\AddAccountFlagsCommand;
use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\Result;
use AqBanking\ExistingAccount;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\Command\AddAccountFlagsCommand
 * @uses \AqBanking\Command\AbstractCommand
 * @uses \AqBanking\Command\ShellCommandExecutor\ResultAnalyzer
 * @uses \AqBanking\Command\ShellCommandExecutor\DefectiveResultException
 * @uses \AqBanking\Command\ShellCommandExecutor\Result
 * @uses \AqBanking\Account
 * @uses \AqBanking\BankCode
 * @uses \AqBanking\ExistingAccount
 */
class AddAccountFlagsCommandTest extends TestCase
{
    /**
     * @throws DefectiveResultException
     *
     * @dataProvider samples
     */
    public function testAddAccountFlags(Result $result, bool $pass): void
    {
        $flags = 'FLAG';
        $uniqueAccountId = 1;

        $account = new ExistingAccount(
            account: new Account(bankCode: new BankCode('50050010'), accountNumber: '1234567890'),
            uniqueAccountId: $uniqueAccountId
        );

        $shellCommandExecutorMock = \Mockery::mock('AqBanking\Command\ShellCommandExecutor');
        $shellCommandExecutorMock
            ->shouldReceive('execute')
            ->with(sprintf('aqhbci-tool4 addaccountflags --account=%s --flags=%s', $uniqueAccountId, $flags))
            ->andReturn($result);

        $sut = new AddAccountFlagsCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);

        if (! $pass) {
            $this->expectException(DefectiveResultException::class);
        }

        $sut->execute($account, $flags);

        if ($pass) {
            $this->assertTrue(true);
        }
    }

    public static function samples(): array
    {
        return [
            'pass' => [new Result([], [], 0), true],
            'error' => [new Result([], ['Error'], 1), false],
        ];
    }
}
