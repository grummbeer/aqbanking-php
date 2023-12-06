<?php

namespace Tests\Command;

use AqBanking\Command\RenderContextFileToXMLCommand;
use AqBanking\Command\ShellCommandExecutor\Result;
use AqBanking\ContextFile;
use PHPUnit\Framework\TestCase;

class RenderContextFileToXMLCommandTest extends TestCase
{
    public function testCanIssueCorrectRenderCommand()
    {
        $this->markTestSkipped('FIXME');

        $contextFile = new ContextFile('/path/to/some/context/file.ctx');

        $shellCommandExecutorMock = \Mockery::mock('AqBanking\Command\ShellCommandExecutor');
        $expectedCommand =
            'aqbanking-cli'
            . ' export'
            . ' --ctxfile=' . $contextFile->getPath()
            . ' --exporter=xmldb';
        $output = [
            '<?xml version="1.0" encoding="utf-8"?>',
            '<ImExporterContext type="group">',
            '</ImExporterContext>',
        ];
        $shellCommandExecutorMock
            ->shouldReceive('execute')->once()
            ->with($expectedCommand)
            ->andReturn(new Result($output, [], 0));

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
            ->andReturn(new Result([], [], 1));

        $sut = new RenderContextFileToXMLCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);

        $this->expectException('RuntimeException');
        $sut->execute(new ContextFile('/path/to/some/context/file.ctx'));
    }

    public function testCanRenderSimpleXML()
    {
        $shellCommandExecutorMock = \Mockery::mock('AqBanking\Command\ShellCommandExecutor');
        $sut = new RenderContextFileToXMLCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);

        $shellCommandExecutorMock
            ->shouldReceive('execute')
            ->once()
            ->andReturn(
                new Result(
                    [file_get_contents('./tests/fixtures/test_context_file_transactions_with_type_transfer.xml')],
                    [],
                    0
                )
            );

        $simpleXML = $sut->execute(
            new ContextFile('/path/to/some/context/file.ctx'),
            true
        );

        $this->assertEquals(
            'DE33123456780000000000',
            (string) $simpleXML->accountInfoList->accountInfo->iban->value
        );

        $this->assertEquals(
            'ASDFFFWWAA',
            (string) $simpleXML->accountInfoList->accountInfo->bic->value
        );

        $this->assertEquals(
            'HARALD MUSTERMANN',
            (string) $simpleXML->accountInfoList->accountInfo->owner->value
        );

        $this->assertEquals(
            'DE15453384569356645534',
            (string) $simpleXML->accountInfoList->accountInfo->transactionList->transaction->remoteIban->value
        );

        $this->assertEquals(
            'WARENHAUS GMBH',
            (string) $simpleXML->accountInfoList->accountInfo->transactionList->transaction->remoteName->value
        );

        $this->assertEquals(
            '2174/100:EUR',
            (string) $simpleXML->accountInfoList->accountInfo->transactionList->transaction->value->value
        );

        $this->assertEquals(
            '0',
            (string) $simpleXML->accountInfoList->accountInfo->transactionList->transaction->executionDay->value
        );

        $this->assertEquals(
            '20220106',
            (string) $simpleXML->accountInfoList->accountInfo->transactionList->transaction->date->value
        );

        $this->assertEquals(
            'Rechnung',
            (string) $simpleXML->accountInfoList->accountInfo->transactionList->transaction->purpose->value
        );

        $this->assertEquals(
            'transfer',
            (string) $simpleXML->accountInfoList->accountInfo->transactionList->transaction->type->value
        );

        $this->assertEquals(
            'pending',
            (string) $simpleXML->accountInfoList->accountInfo->transactionList->transaction->status->value
        );

        $this->assertEquals(
            '2407',
            (string) $simpleXML->accountInfoList->accountInfo->transactionList->transaction->uniqueId->value
        );
    }
}
