<?php

namespace Tests\Command;

use AqBanking\ContextFile;
use PHPUnit\Framework\TestCase;
use AqBanking\Command\ShellCommandExecutor\Result;
use AqBanking\Command\RenderContextFileToXMLCommand;

class RenderContextFileToXMLCommandTest extends TestCase
{
    public function testCanIssueCorrectRenderCommand()
    {
        $contextFile = new ContextFile('/path/to/some/context/file.ctx');

        $shellCommandExecutorMock = \Mockery::mock('AqBanking\Command\ShellCommandExecutor');
        $expectedCommand =
            'aqbanking-cli'
            . ' export'
            . ' --ctxfile=' . $contextFile->getPath()
            . ' --exporter=xmldb';
        $output = array(
            '<?xml version="1.0" encoding="utf-8"?>',
            '<ImExporterContext type="group">',
            '</ImExporterContext>'
        );
        $shellCommandExecutorMock
            ->shouldReceive('execute')->once()
            ->with($expectedCommand)
            ->andReturn(new Result($output, array(), 0));

        $expectedXmlString = implode(PHP_EOL, $output);


        $sut = new RenderContextFileToXMLCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);

        $result = $sut->execute($contextFile);


        $this->assertInstanceOf('DOMDocument', $result);
        $this->assertXmlStringEqualsXmlString($expectedXmlString, $result->saveXML());
    }

    public function testCanHandleUnexpectedOutput()
    {
        $shellCommandExecutorMock = \Mockery::mock('AqBanking\Command\ShellCommandExecutor');
        $shellCommandExecutorMock
            ->shouldReceive('execute')->once()
            ->andReturn(new Result(array(), array(), 1));

        $sut = new RenderContextFileToXMLCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);

        $this->expectException('RuntimeException');
        $sut->execute(new ContextFile('/path/to/some/context/file.ctx'));
    }
}
