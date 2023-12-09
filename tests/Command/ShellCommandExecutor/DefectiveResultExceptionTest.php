<?php

declare(strict_types=1);

namespace Tests\Command\ShellCommandExecutor;

use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\Result;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\Command\ShellCommandExecutor\DefectiveResultException
 * @uses \AqBanking\Command\ShellCommandExecutor\Result
 */
class DefectiveResultExceptionTest extends TestCase
{
    public function testDefectiveResultException(): void
    {
        $result = new Result(
            output: [],
            errors: [
                'Error line 1',
                'Error line 2',
            ],
            returnVar: 0
        );

        $shellCommand = 'aqbanking-cli listaccounts';
        $message = 'Exception message';
        $sut = new DefectiveResultException(
            message: $message,
            result: $result,
            shellCommand: $shellCommand
        );

        $this->assertInstanceOf(Exception::class, $sut);
        $this->assertSame($message .
            ' - Command: ' . $shellCommand .
            ' - Errors: ' . implode(PHP_EOL, $result->getErrors()), $sut->getMessage());
        $this->assertSame($result, $sut->getResult());
        $this->assertSame($shellCommand, $sut->getShellCommand());
    }
}
