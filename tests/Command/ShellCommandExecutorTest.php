<?php

namespace Tests\Command;

use AqBanking\Command\ShellCommandExecutor;
use PHPUnit\Framework\TestCase;

class ShellCommandExecutorTest extends TestCase
{
    public function testCanExecuteCommand()
    {
        $sut = new ShellCommandExecutor();

        $result = $sut->execute('echo "whatever"');

        $this->assertEquals(["whatever"], $result->getOutput());
        $this->assertEquals([], $result->getErrors());
        $this->assertEquals(0, $result->getReturnVar());
    }

    public function testCanTellAboutErrors()
    {
        $sut = new ShellCommandExecutor();

        $result = $sut->execute("any-unknown-command");

        $this->assertEquals([], $result->getOutput());
        $this->assertNotEquals(0, \count($result->getErrors()));
        $this->assertNotEquals(0, $result->getReturnVar());
    }
}
