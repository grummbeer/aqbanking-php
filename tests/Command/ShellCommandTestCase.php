<?php

namespace Tests\Command;

use Mockery;
use PHPUnit\Framework\TestCase;

class ShellCommandTestCase extends TestCase
{
    /**
     * @return \Mockery\MockInterface
     */
    protected function getShellCommandExecutorMock()
    {
        return Mockery::mock('AqBanking\Command\ShellCommandExecutor');
    }
}
