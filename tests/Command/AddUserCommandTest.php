<?php

declare(strict_types=1);

namespace Tests\Command;

use AqBanking\Bank;
use AqBanking\BankCode;
use AqBanking\Command\AddUserCommand;
use AqBanking\Command\AddUserCommand\UserAlreadyExistsException;
use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\Result;
use AqBanking\User;

/**
 * @covers \AqBanking\Command\AddUserCommand
 * @uses \AqBanking\Bank
 * @uses \AqBanking\BankCode
 * @uses \AqBanking\User
 * @uses \AqBanking\Command\AbstractCommand
 * @uses \AqBanking\Command\ShellCommandExecutor\DefectiveResultException
 * @uses \AqBanking\Command\ShellCommandExecutor\Result
 * @uses \AqBanking\Command\ShellCommandExecutor\ResultAnalyzer
 */
class AddUserCommandTest extends ShellCommandTestCase
{
    /**
     * @throws UserAlreadyExistsException
     * @throws DefectiveResultException
     */
    public function testCanAddAqBankingUser(): void
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
            ->andReturn(new Result([], [], 0));

        $sut = new AddUserCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);

        $sut->execute($user);

        // To satisfy PHPUnit's "strict" mode - if Mockery didn't throw an exception until here, everything is fine
        $this->assertTrue(true);
    }

    /**
     * @throws DefectiveResultException
     */
    public function testThrowsExceptionIfUserAlreadyExists(): void
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
            ->andReturn(new Result([], [], AddUserCommand::RETURN_VAR_USER_ALREADY_EXISTS));

        $sut = new AddUserCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);

        $this->expectException('\AqBanking\Command\AddUserCommand\UserAlreadyExistsException');
        $sut->execute(new User($userId, $userName, new Bank(new BankCode($bankCodeString), $hbciUrl)));
    }

    /**
     * @throws UserAlreadyExistsException
     */
    public function testThrowsExceptionOnUnexpectedResult(): void
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
            ->andReturn(new Result([], [], 127));

        $sut = new AddUserCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);

        $this->expectException('AqBanking\Command\ShellCommandExecutor\DefectiveResultException');
        $sut->execute(new User($userId, $userName, new Bank(new BankCode($bankCodeString), $hbciUrl)));
    }
}
