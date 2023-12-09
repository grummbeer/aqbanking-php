<?php

declare(strict_types=1);

namespace Tests\Command;

use AqBanking\Account;
use AqBanking\BankCode;
use AqBanking\Command\RequestCommand;
use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\Result;
use AqBanking\ContextFile;
use Mockery;
use Mockery\MockInterface;

/**
 * @covers \AqBanking\Command\RequestCommand
 * @uses \AqBanking\Command\AbstractCommand
 * @uses \AqBanking\Account
 * @uses \AqBanking\BankCode
 * @uses \AqBanking\ContextFile
 * @uses \AqBanking\Command\ShellCommandExecutor\DefectiveResultException
 * @uses \AqBanking\Command\ShellCommandExecutor\Result
 * @uses \AqBanking\Command\ShellCommandExecutor\ResultAnalyzer
 */
class RequestCommandTest extends ShellCommandTestCase
{
    /**
     * @throws DefectiveResultException
     */
    public function testCanExecute(): void
    {
        $accountNumber = '12345678';
        $bankCodeString = '23456789';
        $bankCode = new BankCode($bankCodeString);
        $account = new Account($bankCode, $accountNumber);

        $pathToContextFile = '/path/to/context_file';
        $contextFile = new ContextFile($pathToContextFile);

        $pathToPinFile = '/path/to/pinfile';
        $pinFileMock = $this->getPinFileMock($pathToPinFile);

        $shellCommandExecutorMock = $this->getShellCommandExecutorMock();
        // This is the actual test
        $expectedCommand =
            "aqbanking-cli"
            . " --noninteractive"
            . " --acceptvalidcerts"
            . " --pinfile=" . $pathToPinFile
            . " request"
            . " --bank=" . $bankCodeString
            . " --account=" . $accountNumber
            . " --ctxfile=" . $pathToContextFile
            . " --transactions"
            . " --balance";
        $shellCommandExecutorMock
            ->shouldReceive('execute')->once()
            ->with($expectedCommand)
            ->andReturn(new Result([], [], 0));

        $sut = new RequestCommand($account, $contextFile, $pinFileMock);
        $sut->setShellCommandExecutor($shellCommandExecutorMock);
        $sut->execute();

        // To satisfy PHPUnit's "strict" mode - if Mockery didn't throw an exception until here, everything is fine
        $this->assertTrue(true);
    }

    /**
     * @throws DefectiveResultException
     */
    public function testCanExecuteWithFromDate(): void
    {
        $accountNumber = '12345678';
        $bankCodeString = '23456789';
        $bankCode = new BankCode($bankCodeString);
        $account = new Account($bankCode, $accountNumber);

        $pathToContextFile = '/path/to/context_file';
        $contextFile = new ContextFile($pathToContextFile);

        $pathToPinFile = '/path/to/pinfile';
        $pinFileMock = $this->getPinFileMock($pathToPinFile);

        $fromDate = new \DateTime('yesterday');

        $shellCommandExecutorMock = $this->getShellCommandExecutorMock();
        // This is the actual test
        $expectedCommand =
            "aqbanking-cli"
            . " --noninteractive"
            . " --acceptvalidcerts"
            . " --pinfile=" . $pathToPinFile
            . " request"
            . " --bank=" . $bankCodeString
            . " --account=" . $accountNumber
            . " --ctxfile=" . $pathToContextFile
            . " --transactions"
            . " --balance"
            . " --fromdate=" . $fromDate->format('Ymd');
        $shellCommandExecutorMock
            ->shouldReceive('execute')->once()
            ->with($expectedCommand)
            ->andReturn(new Result([], [], 0));

        $sut = new RequestCommand($account, $contextFile, $pinFileMock);
        $sut->setShellCommandExecutor($shellCommandExecutorMock);
        $sut->execute($fromDate);

        // To satisfy PHPUnit's "strict" mode - if Mockery didn't throw an exception until here, everything is fine
        $this->assertTrue(true);
    }

    public function testThrowsExceptionOnUnexpectedResult(): void
    {
        $accountNumber = '12345678';
        $bankCodeString = '23456789';
        $bankCode = new BankCode($bankCodeString);
        $account = new Account($bankCode, $accountNumber);

        $pathToContextFile = '/path/to/context_file';
        $contextFile = new ContextFile($pathToContextFile);

        $pathToPinFile = '/path/to/pinfile';
        $pinFileMock = $this->getPinFileMock($pathToPinFile);

        $shellCommandExecutorMock = $this->getShellCommandExecutorMock();
        $shellCommandExecutorMock
            ->shouldReceive('execute')->once()
            ->andReturn(new Result([], ['some unexpected output'], 0));

        $sut = new RequestCommand($account, $contextFile, $pinFileMock);
        $sut->setShellCommandExecutor($shellCommandExecutorMock);
        $this->expectException('AqBanking\Command\ShellCommandExecutor\DefectiveResultException');
        $sut->execute();
    }

    private function getPinFileMock(string $pathToPinFile): MockInterface
    {
        $pinFileMock = Mockery::mock('AqBanking\PinFile\PinFile');
        $pinFileMock
            ->shouldReceive('getPath')
            ->andReturn($pathToPinFile);

        return $pinFileMock;
    }
}
