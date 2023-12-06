<?php

declare(strict_types=1);

namespace Tests\Command\ShellCommandExecutor;

use AqBanking\Command\ShellCommandExecutor\Result;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\Command\ShellCommandExecutor\Result
 */
class ResultTest extends TestCase
{
    public function testResult(): void
    {
        $sut = new Result(output: ['Line 1', 'Line 2'], errors: ['Line 1'], returnVar: 0);

        $this->assertCount(2, $sut->getOutput());
        $this->assertCount(1, $sut->getErrors());

        $this->assertSame(0, $sut->getReturnVar());
    }
}
