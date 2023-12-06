<?php

declare(strict_types=1);

namespace Tests;

use AqBanking\Account;
use AqBanking\BankCode;
use AqBanking\ContextXmlRenderer;
use AqBanking\Transaction;
use Exception;
use Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\ContextXmlRenderer
 * @uses \AqBanking\ContentXmlRenderer\MoneyElementRenderer
 */
class ContextXmlRendererTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCanRenderTransfers()
    {
        $this->markTestSkipped('temporarily skipped due the unclear behaviour of Account<>getTransaction');

        $fixture = file_get_contents(__DIR__ . '/fixtures/test_context_file_transactions_with_type_transfer.xml');
        $domDocument = new \DOMDocument();
        $domDocument->loadXML($fixture);

        $sut = new ContextXmlRenderer($domDocument);

        $localAccount = new Account(new BankCode('12345678'), '404072100', 'HARALD MUSTERMANN');
        $expectedTransactions = [
            new Transaction(
                $localAccount,
                new Account(new BankCode(''), '', "WARENHAUS GMBH"),
                'transfer',
                'Rechnung',
                null,
                new \DateTime('2022-01-06 00:00:00', new \DateTimeZone('UTC')),
                Money::EUR(2174),
                '',
                ''
            )];

        $this->assertEquals($expectedTransactions, $sut->getTransactions());
    }

    /**
     * @throws Exception
     */
    public function testCanRenderTransactions()
    {
        $this->markTestSkipped('temporarily skipped due the unclear behaviour of Account<>getTransaction');

        $fixture = file_get_contents(__DIR__ . '/fixtures/test_context_file_transactions.xml');
        $domDocument = new \DOMDocument();
        $domDocument->loadXML($fixture);

        $sut = new ContextXmlRenderer($domDocument);

        $localAccount = new Account(new BankCode('32151229'), '12112345');
        $expectedTransactions = [
            new Transaction(
                $localAccount,
                new Account(new BankCode('MALADE51KOB'), 'DE62570501200000012345', 'Sehr sehr langer Kontoinhab'),
                'statement',
                '5828201 01.06.2013',
                new \DateTime('2021-12-01 00:00:00', new \DateTimeZone('UTC')),
                new \DateTime('2021-12-01 00:00:00', new \DateTimeZone('UTC')),
                Money::EUR(-1111),
                '97186',
                'KREW+'
            )];

        $this->assertEquals($expectedTransactions, $sut->getTransactions());
    }

    /**
     * @throws Exception
     */
    public function testThrowsExceptionIfDataContainsReservedChar()
    {
        $fixture = file_get_contents(__DIR__ . '/fixtures/test_context_file_transactions_with_reserved_char.xml');
        $domDocument = new \DOMDocument();
        $domDocument->loadXML($fixture);

        $sut = new ContextXmlRenderer($domDocument);

        $this->expectException('\RuntimeException');
        $sut->getTransactions();
    }

    /**
     * @throws Exception
     */
    public function testThrowsExceptionIfAmountIsMalformed()
    {
        $fixture = file_get_contents(__DIR__ . '/fixtures/test_context_file_transactions_with_malformed_amount.xml');
        $domDocument = new \DOMDocument();
        $domDocument->loadXML($fixture);

        $sut = new ContextXmlRenderer($domDocument);

        $this->expectException('\AqBanking\RuntimeException');
        $sut->getTransactions();
    }
}
