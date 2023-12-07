<?php

declare(strict_types=1);

namespace Tests\Command;

use AqBanking\Command\ListAccountsCommand;
use AqBanking\Command\ShellCommandExecutor\Result;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \AqBanking\Command\ListAccountsCommand
 * @uses \AqBanking\Command\AbstractCommand
 * @uses \AqBanking\Command\ShellCommandExecutor\Result
 */
class ListAccountsCommandTest extends TestCase
{
    private static array $output = [
        'Account 0: Bank: 10010010 Account Number: 10000000  SubAccountId: (none)  Account Type: bank LocalUniqueId: 1',
        'Account 1: Bank: 20020020 Account Number: 200000  SubAccountId: (none)  Account Type: savings LocalUniqueId: 2',
    ];

    /**
     * @dataProvider samples
     */
    public function testListAccounts(Result $result, bool $pass): void
    {
        $shellCommandExecutorMock = \Mockery::mock('AqBanking\Command\ShellCommandExecutor');
        $shellCommandExecutorMock
            ->shouldReceive('execute')
            ->with('aqhbci-tool4 listaccounts -v')
            ->andReturn($result);

        $sut = new ListAccountsCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);

        if (! $pass) {
            $this->expectException(RuntimeException::class);
        }

        $accounts = $sut->execute();

        if ($pass) {
            $this->assertIsArray($accounts);
            $this->assertCount(2, $accounts);
            $this->assertSame([
                ListAccountsCommand::BANK => '10010010',
                ListAccountsCommand::NUMBER => '10000000',
                ListAccountsCommand::UNIQUE_ID => 1,
            ], $accounts[0]);
            $this->assertSame([
                ListAccountsCommand::BANK => '20020020',
                ListAccountsCommand::NUMBER => '200000',
                ListAccountsCommand::UNIQUE_ID => 2,
            ], $accounts[1]);
        }
    }

    public static function samples(): array
    {
        return [
            'pass' => [new Result(static::$output, [], 0), true],
            'error' => [new Result([], ['Error'], 1), false],
        ];
    }
}
