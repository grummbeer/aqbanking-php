<?php

namespace Tests\Command;

use AqBanking\Command\CheckAqBankingCommand;
use AqBanking\Command\ShellCommandExecutor\Result;
use PHPUnit\Framework\TestCase;

class CheckAqBankingCommandTest extends TestCase
{
    public function testCanTellIfAqBankingIsInstalled()
    {
        $shellCommandExecutorMock = \Mockery::mock('AqBanking\Command\ShellCommandExecutor');
        $shellCommandExecutorMock
            ->shouldReceive('execute')
            ->with('aqbanking-cli --help')
            ->andReturn(new Result([], [], 0));
        $shellCommandExecutorMock
            ->shouldReceive('execute')
            ->with('aqbanking-config --vstring')
            ->andReturn(new Result(['5.0.24'], [], 0));

        $sut = new CheckAqBankingCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);
        $sut->execute();

        // To satisfy PHPUnit's "strict" mode - if Mockery didn't throw an exception until here, everything is fine
        $this->assertTrue(true);
    }

    public function testCanTellIfAqBankingIsNotInstalled()
    {
        $shellCommandExecutorMock = \Mockery::mock('AqBanking\Command\ShellCommandExecutor');
        $shellCommandExecutorMock
            ->shouldReceive('execute')->once()
            ->with('aqbanking-cli --help')
            ->andReturn(new Result([], [], 127));

        $sut = new CheckAqBankingCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);
        $this->expectException('\AqBanking\Command\CheckAqBankingCommand\AqBankingNotRespondingException');
        $sut->execute();
    }

    public function testCanHandleVeryOldAqBankingVersionWithoutAqBankingConfig()
    {
        $shellCommandExecutorMock = \Mockery::mock('AqBanking\Command\ShellCommandExecutor');
        $shellCommandExecutorMock
            ->shouldReceive('execute')
            ->with('aqbanking-cli --help')
            ->andReturn(new Result([], [], 0));
        $shellCommandExecutorMock
            ->shouldReceive('execute')
            ->with('aqbanking-config --vstring')
            ->andReturn(new Result([], [], 127));

        $sut = new CheckAqBankingCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);
        $this->expectException('\AqBanking\Command\CheckAqBankingCommand\AqBankingVersionTooOldException');
        $sut->execute();
    }

    public function testCanTellIfAqBankingVersionIsTooOld()
    {
        $shellCommandExecutorMock = \Mockery::mock('AqBanking\Command\ShellCommandExecutor');
        $shellCommandExecutorMock
            ->shouldReceive('execute')
            ->with('aqbanking-cli --help')
            ->andReturn(new Result([], [], 0));
        $shellCommandExecutorMock
            ->shouldReceive('execute')
            ->with('aqbanking-config --vstring')
            ->andReturn(new Result(['5.0.23'], [], 0));

        $sut = new CheckAqBankingCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);
        $this->expectException('\AqBanking\Command\CheckAqBankingCommand\AqBankingVersionTooOldException');
        $sut->execute();
    }
}
