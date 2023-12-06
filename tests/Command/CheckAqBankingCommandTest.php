<?php

declare(strict_types=1);

namespace Tests\Command;

use AqBanking\Command\CheckAqBankingCommand;
use AqBanking\Command\CheckAqBankingCommand\AqBankingNotRespondingException;
use AqBanking\Command\CheckAqBankingCommand\AqBankingVersionTooOldException;
use AqBanking\Command\ShellCommandExecutor\Result;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\Command\CheckAqBankingCommand
 * @uses \AqBanking\Command\AbstractCommand
 * @uses \AqBanking\Command\ShellCommandExecutor\Result
 */
class CheckAqBankingCommandTest extends TestCase
{
    /**
     * @throws AqBankingVersionTooOldException
     * @throws AqBankingNotRespondingException
     */
    public function testCanTellIfAqBankingIsInstalled(): void
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

    /**
     * @throws AqBankingVersionTooOldException
     */
    public function testCanTellIfAqBankingIsNotInstalled(): void
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

    /**
     * @throws AqBankingNotRespondingException
     */
    public function testCanHandleVeryOldAqBankingVersionWithoutAqBankingConfig(): void
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

    /**
     * @throws AqBankingNotRespondingException
     */
    public function testCanTellIfAqBankingVersionIsTooOld(): void
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
