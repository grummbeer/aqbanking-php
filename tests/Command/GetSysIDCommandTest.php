<?php

namespace Tests\Command;

use AqBanking\Bank;
use AqBanking\BankCode;
use AqBanking\Command\GetSysIDCommand;
use AqBanking\Command\ShellCommandExecutor\Result;
use AqBanking\ExistingUser;
use AqBanking\PinFile\PinFile;
use AqBanking\User;

class GetSysIDCommandTest extends ShellCommandTestCase
{
    public function testPollsSysID()
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

    public function testThrowsExceptionOnUnexpectedResult()
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
