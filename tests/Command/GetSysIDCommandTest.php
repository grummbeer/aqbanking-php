<?php

declare(strict_types=1);

namespace Tests\Command;

use AqBanking\Bank;
use AqBanking\BankCode;
use AqBanking\Command\GetSysIDCommand;
use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\Result;
use AqBanking\ExistingUser;
use AqBanking\PinFile\PinFile;
use AqBanking\User;

/**
 * @covers \AqBanking\Command\GetSysIDCommand
 * @uses \AqBanking\Command\AbstractCommand
 * @uses \AqBanking\Command\ShellCommandExecutor\DefectiveResultException
 * @uses \AqBanking\Command\ShellCommandExecutor\Result
 * @uses \AqBanking\Command\ShellCommandExecutor\ResultAnalyzer
 * @uses \AqBanking\User
 * @uses \AqBanking\Bank
 * @uses \AqBanking\BankCode
 * @uses \AqBanking\ExistingUser
 * @uses \AqBanking\PinFile\PinFile
 */
class GetSysIDCommandTest extends ShellCommandTestCase
{
    /**
     * @throws DefectiveResultException
     */
    public function testPollsSysID(): void
    {
        $userId = 'mustermann';
        $uniqueUserId = 123;
        $userName = 'Max Mustermann';
        $bankCodeString = '12345678';
        $hbciUrl = 'https://hbci.example.com';

        $bankCode = new BankCode($bankCodeString);
        $bank = new Bank($bankCode, $hbciUrl);
        $user = new User($userId, $userName, $bank);
        $existingUser = new ExistingUser($user, $uniqueUserId);
        $pinFile = new PinFile('/path/to/pinfile/dir', $user);

        $shellCommandExecutorMock = $this->getShellCommandExecutorMock();

        $expectedCommand =
            'aqhbci-tool4'
            . ' --pinfile=' . escapeshellarg($pinFile->getPath())
            . ' --noninteractive'
            . ' --acceptvalidcerts'
            . ' getsysid'
            . ' --user=' . $uniqueUserId
        ;

        $shellCommandExecutorMock
            ->shouldReceive('execute')->once()
            ->with($expectedCommand)
            ->andReturn(new Result([], [], 0));

        $sut = new GetSysIDCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);
        $sut->execute($existingUser, $pinFile);

        // To satisfy PHPUnit's "strict" mode - if Mockery didn't throw an exception until here, everything is fine
        $this->assertTrue(true);
    }

    public function testThrowsExceptionOnUnexpectedResult(): void
    {
        $userId = 'mustermann';
        $uniqueUserId = 123;
        $userName = 'Max Mustermann';
        $bankCodeString = '12345678';
        $hbciUrl = 'https://hbci.example.com';

        $bankCode = new BankCode($bankCodeString);
        $bank = new Bank($bankCode, $hbciUrl);
        $user = new User($userId, $userName, $bank);
        $existingUser = new ExistingUser($user, $uniqueUserId);

        $pinFile = new PinFile('/path/to/pinfile/dir', $user);

        $shellCommandExecutorMock = $this->getShellCommandExecutorMock();

        $expectedCommand =
            'aqhbci-tool4'
            . ' --pinfile=' . escapeshellarg($pinFile->getPath())
            . ' --noninteractive'
            . ' --acceptvalidcerts'
            . ' getsysid'
            . ' --user=' . $uniqueUserId
        ;

        $shellCommandExecutorMock
            ->shouldReceive('execute')->once()
            ->with($expectedCommand)
            ->andReturn(new Result([], [], 1));

        $sut = new GetSysIDCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);
        $this->expectException('\AqBanking\Command\ShellCommandExecutor\DefectiveResultException');
        $sut->execute($existingUser, $pinFile);
    }
}
