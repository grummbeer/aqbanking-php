<?php

namespace Tests\Command\ShellCommandExecutor;

use AqBanking\Command\ShellCommandExecutor\Result;
use AqBanking\Command\ShellCommandExecutor\ResultAnalyzer;
use PHPUnit\Framework\TestCase;

class ResultAnalyzerTest extends TestCase
{
    public function testEmptyResultIsNoError()
    {
        $result = new Result([], [], 0);

        $sut = new ResultAnalyzer();

        $this->assertFalse($sut->isDefectiveResult($result));
    }

    public function testRecognizesErrorByResultVar()
    {
        $result = new Result([], [], 1);

        $sut = new ResultAnalyzer();

        $this->assertTrue($sut->isDefectiveResult($result));
    }

    public function testCanTellCorrectPollResult()
    {
        $errors = [
            '5:2013/07/22 11-32-32:aqbanking(39873):abgui.c:  182: Automatically accepting valid new certificate [40:BD:81:8B:76:27:1A:58:5C:B7:68:46:1E:CB:F2:FD]',
            '5:2013/07/22 11-32-33:aqbanking(39873):abgui.c:  182: Automatically accepting valid new certificate [40:BD:81:8B:76:27:1A:58:5C:B7:68:46:1E:CB:F2:FD]',
            '5:2013/07/22 11-32-33:aqbanking(39873):abgui.c:  182: Automatically accepting valid new certificate [40:BD:81:8B:76:27:1A:58:5C:B7:68:46:1E:CB:F2:FD]',
            '5:2013/07/22 11-32-33:aqbanking(39873):abgui.c:  182: Automatically accepting valid new certificate [40:BD:81:8B:76:27:1A:58:5C:B7:68:46:1E:CB:F2:FD]',
            '5:2013/07/22 11-32-34:aqbanking(39873):abgui.c:  182: Automatically accepting valid new certificate [40:BD:81:8B:76:27:1A:58:5C:B7:68:46:1E:CB:F2:FD]',
            '5:2013/07/22 11-32-34:aqbanking(39873):abgui.c:  182: Automatically accepting valid new certificate [40:BD:81:8B:76:27:1A:58:5C:B7:68:46:1E:CB:F2:FD]',
        ];
        $result = new Result([], $errors, 0);

        $sut = new ResultAnalyzer();

        $this->assertFalse($sut->isDefectiveResult($result));
    }

    public function testCanTellDefectivePollResult()
    {
        $errors = [
            '5:2013/07/22 11-31-44:aqbanking(39859):abgui.c:  182: Automatically accepting valid new certificate [40:BD:81:8B:76:27:1A:58:5C:B7:68:46:1E:CB:F2:FD]',
            '3:2013/07/22 11-31-44:aqhbci(39859):outbox.c: 1390: Error performing queue (-2)',
            '5:2013/07/22 11-31-44:aqbanking(39859):./banking_online.c:  119: Error executing backend\'s queue',
            '4:2013/07/22 11-31-44:aqbanking(39859):./banking_online.c:  137: Not a single job successfully executed',
        ];
        $result = new Result([], $errors, 0);

        $sut = new ResultAnalyzer();

        $this->assertTrue($sut->isDefectiveResult($result));
    }
}
