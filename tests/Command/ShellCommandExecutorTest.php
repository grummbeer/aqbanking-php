<?php

namespace Tests\Command;

use AqBanking\Command\ShellCommandExecutor;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\Command\ShellCommandExecutor
 * @uses \AqBanking\Command\ShellCommandExecutor\Result
 */
class ShellCommandExecutorTest extends TestCase
{
    public function testCanExecuteCommand(): void
    {
        $sut = new ShellCommandExecutor();

        $result = $sut->execute('echo "whatever"');

        $this->assertEquals(["whatever"], $result->getOutput());
        $this->assertEquals([], $result->getErrors());
        $this->assertEquals(0, $result->getReturnVar());
    }

    public function testCanTellAboutErrors(): void
    {
        $sut = new ShellCommandExecutor();

        $result = $sut->execute("any-unknown-command");

        $this->assertEquals([], $result->getOutput());
        $this->assertNotEquals(0, \count($result->getErrors()));
        $this->assertNotEquals(0, $result->getReturnVar());
    }
}
