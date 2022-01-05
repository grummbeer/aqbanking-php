<?php

namespace Tests\Command;

use AqBanking\Bank;
use AqBanking\User;
use AqBanking\BankCode;
use AqBanking\Command\AddUserCommand;
use AqBanking\Command\ShellCommandExecutor\Result;

class AddUserCommandTest extends ShellCommandTestCase
{
    public function testCanAddAqBankingUser()
    {
        $userId = 'mustermann';
        $userName = 'Max Mustermann';
        $bankCodeString = '12345678';
        $hbciUrl = 'https://hbci.example.com';

        $bankCode = new BankCode($bankCodeString);
        $bank = new Bank($bankCode, $hbciUrl);
        $user = new User($userId, $userName, $bank);

        $shellCommandExecutorMock = $this->getShellCommandExecutorMock();

        $expectedCommand =
            'aqhbci-tool4'
            . ' adduser'
            . ' --username="' . $userName . '"'
            . ' --bank=' . $bankCodeString
            . ' --user=' . $userId
            . ' --tokentype=pintan'
            . ' --server=' . $hbciUrl
        ;

        $shellCommandExecutorMock
            ->shouldReceive('execute')->once()
            ->with($expectedCommand)
            ->andReturn(new Result(array(), array(), 0));

        $sut = new AddUserCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);

        $sut->execute($user);

        // To satisfy PHPUnit's "strict" mode - if Mockery didn't throw an exception until here, everything is fine
        $this->assertTrue(true);
    }

    public function testThrowsExceptionIfUserAlreadyExists()
    {
        $userId = 'mustermann';
        $userName = 'Max Mustermann';
        $bankCodeString = '12345678';
        $hbciUrl = 'https://hbci.example.com';

        $shellCommandExecutorMock = $this->getShellCommandExecutorMock();

        $expectedCommand =
            'aqhbci-tool4'
            . ' adduser'
            . ' --username="' . $userName . '"'
            . ' --bank=' . $bankCodeString
            . ' --user=' . $userId
            . ' --tokentype=pintan'
            . ' --server=' . $hbciUrl
        ;

        $shellCommandExecutorMock
            ->shouldReceive('execute')->once()
            ->with($expectedCommand)
            ->andReturn(new Result(array(), array(), AddUserCommand::RETURN_VAR_USER_ALREADY_EXISTS));

        $sut = new AddUserCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);

        $this->expectException('\AqBanking\Command\AddUserCommand\UserAlreadyExistsException');
        $sut->execute(new User($userId, $userName, new Bank(new BankCode($bankCodeString), $hbciUrl)));
    }

    public function testThrowsExceptionOnUnexpectedResult()
    {
        $userId = 'mustermann';
        $userName = 'Max Mustermann';
        $bankCodeString = '12345678';
        $hbciUrl = 'https://hbci.example.com';

        $shellCommandExecutorMock = $this->getShellCommandExecutorMock();

        $expectedCommand =
            'aqhbci-tool4'
            . ' adduser'
            . ' --username="' . $userName . '"'
            . ' --bank=' . $bankCodeString
            . ' --user=' . $userId
            . ' --tokentype=pintan'
            . ' --server=' . $hbciUrl
        ;

        $shellCommandExecutorMock
            ->shouldReceive('execute')->once()
            ->with($expectedCommand)
            ->andReturn(new Result(array(), array(), 127));

        $sut = new AddUserCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);

        $this->expectException('AqBanking\Command\ShellCommandExecutor\DefectiveResultException');
        $sut->execute(new User($userId, $userName, new Bank(new BankCode($bankCodeString), $hbciUrl)));
    }
}
