<?php

declare(strict_types=1);

namespace Tests\Command;

use AqBanking\Account;
use AqBanking\BankCode;
use AqBanking\Command\SepaTransferCommand;
use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\Result;
use AqBanking\ContextFile;
use Mockery;
use Mockery\MockInterface;

/**
 * @covers \AqBanking\Command\SepaTransferCommand
 * @uses \AqBanking\Command\AbstractCommand
 * @uses \AqBanking\Command\ShellCommandExecutor\Result
 * @uses \AqBanking\Command\ShellCommandExecutor\ResultAnalyzer
 * @uses \AqBanking\Account
 * @uses \AqBanking\BankCode
 * @uses \AqBanking\ContextFile
 */
class SepaTransferCommandTest extends ShellCommandTestCase
{
    /**
     * @throws DefectiveResultException
     */
    public function testCanExecute(): void
    {
        $rname = "MrRandom";
        $riban = "DE21344423423";
        $value = "100/100:EUR";
        $purpose = "Some random purpose";

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
            . " sepatransfer"
            . " --bank=" . $bankCodeString
            . " --account=" . $accountNumber
            . " --ctxfile=" . $pathToContextFile
            . " --rname='" . $rname . "'"
            . " --riban=" . $riban
            . " --value=" . $value
            . " --purpose='" . $purpose . "'";

        $shellCommandExecutorMock
            ->shouldReceive('execute')->once()
            ->with($expectedCommand)
            ->andReturn(new Result([], [], 0));

        $sut = new SepaTransferCommand($account, $contextFile, $pinFileMock);
        $sut->setShellCommandExecutor($shellCommandExecutorMock);
        $sut->execute(
            $rname,
            $riban,
            $value,
            $purpose
        );
        $this->assertTrue(true);
    }

    public function testAcceptsValidOutput(): void
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
            ->andReturn(new Result([], ['3:2022/01/06 05-26-00:aqhbci(42):jobtransferbase.c: 1036: Selecting PAIN format [urn:iso:std:iso:20022:tech:xsd:pain.001.001.03]'], 0));

        $sut = new SepaTransferCommand($account, $contextFile, $pinFileMock);
        $sut->setShellCommandExecutor($shellCommandExecutorMock);

        // No exception has been thrown for a valid error output by aqbanking
        $this->assertTrue(true);
    }

    /**
     * @param string $pathToPinFile
     */
    private function getPinFileMock($pathToPinFile): MockInterface
    {
        $pinFileMock = Mockery::mock('AqBanking\PinFile\PinFile');
        $pinFileMock
            ->shouldReceive('getPath')
            ->andReturn($pathToPinFile);

        return $pinFileMock;
    }
}
