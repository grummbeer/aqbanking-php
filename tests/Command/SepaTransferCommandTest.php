<?php

namespace Tests\Command;

use AqBanking\Account;
use AqBanking\BankCode;
use AqBanking\ContextFile;
use AqBanking\Command\SepaTransferCommand;
use Tests\Command\ShellCommandTestCase;
use AqBanking\Command\ShellCommandExecutor\Result;

class SepaTransferCommandTest extends ShellCommandTestCase
{
    public function testCanExecute()
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
            ->andReturn(new Result(array(), array(), 0));

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

    public function testAcceptsValidOutput() {
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
            ->andReturn(new Result(array(), array('3:2022/01/06 05-26-00:aqhbci(42):jobtransferbase.c: 1036: Selecting PAIN format [urn:iso:std:iso:20022:tech:xsd:pain.001.001.03]'), 0));

        $sut = new SepaTransferCommand($account, $contextFile, $pinFileMock);
        $sut->setShellCommandExecutor($shellCommandExecutorMock);

        // No exception has been thrown for a valid error output by aqbanking
        $this->assertTrue(true);

    }


    /**
     * @param string $pathToPinFile
     * @return \Mockery\MockInterface
     */
    private function getPinFileMock($pathToPinFile)
    {
        $pinFileMock = \Mockery::mock('AqBanking\PinFile\PinFile');
        $pinFileMock
            ->shouldReceive('getPath')
            ->andReturn($pathToPinFile);

        return $pinFileMock;
    }
}