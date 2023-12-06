<?php

declare(strict_types=1);

namespace AqBanking;

use AqBanking\ContentXmlRenderer\MoneyElementRenderer;
use DateTime;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Exception;
use Money\Money;

class ContextXmlRenderer
{
    private DOMDocument $domDocument;

    private DOMXPath $xPath;

    private MoneyElementRenderer $moneyElementRenderer;

    public function __construct(DOMDocument $domDocument)
    {
        $this->domDocument = $domDocument;
        $this->xPath = new DOMXPath($domDocument);
        $this->moneyElementRenderer = new MoneyElementRenderer();
    }

    /**
     * @throws Exception
     *
     * @return array<int, Transaction>
     */
    public function getTransactions(): array
    {
        $transactionNodes = $this->domDocument->getElementsByTagName('transaction');
        $transactions = [];

        foreach ($transactionNodes as $transactionNode) {
            $localBankCode = $this->renderMultiLineElement(
                $this->xPath->query('localBankCode/value', $transactionNode)
            );
            $localAccountNumber = $this->renderMultiLineElement(
                $this->xPath->query('localIban/value', $transactionNode)
            );
            $localName = $this->renderMultiLineElement($this->xPath->query('localName/value', $transactionNode));

            $remoteBankCode = $this->renderMultiLineElement(
                $this->xPath->query('remoteBankCode/value', $transactionNode)
            );
            $remoteAccountNumber = $this->renderMultiLineElement(
                $this->xPath->query('remoteIban/value', $transactionNode)
            );
            $remoteName = $this->renderMultiLineElement($this->xPath->query('remoteName/value', $transactionNode));

            $purpose = $this->renderMultiLineElement($this->xPath->query('purpose/value', $transactionNode));

            $valutaDate = $this->renderDateElement($this->xPath->query('valutaDate', $transactionNode)->item(0));
            $date = $this->renderDateElement($this->xPath->query('date', $transactionNode)->item(0));

            $value = $this->renderMoneyElement($this->xPath->query('value', $transactionNode)->item(0));

            $primaNota = $this->renderMultiLineElement(
                $this->xPath->query('primanota/value', $transactionNode)
            );

            $customerRef = $this->renderMultiLineElement(
                $this->xPath->query('customerReference/value', $transactionNode)
            );

            $type = $this->renderMultiLineElement(
                $this->xPath->query('type', $transactionNode)
            );

            $transactions[] = new Transaction(
                new Account(new BankCode($localBankCode), $localAccountNumber, $localName),
                new Account(new BankCode($remoteBankCode), $remoteAccountNumber, $remoteName),
                $type,
                $purpose,
                $valutaDate,
                $date,
                $value,
                $primaNota,
                $customerRef
            );
        }

        return $transactions;
    }

    /**
     * @throws Exception
     *
     * @return array<int, Balance>
     */
    public function getBalances(): array
    {
        $balanceNodes = $this->domDocument->getElementsByTagName('balance');
        $balances = [];

        foreach ($balanceNodes as $balanceNode) {
            /** @var DOMElement $balanceNode */
            $date = $this->renderDateElement($this->xPath->query('date', $balanceNode)->item(0));
            $value = $this->renderMoneyElement($this->xPath->query('value', $balanceNode)->item(0));
            $type = $this->renderSimpleTextElement($this->xPath->query('type', $balanceNode));

            $balances[] = new Balance(date: $date, value: $value, type: $type);
        }

        return $balances;
    }

    private function renderMultiLineElement(DOMNodeList $nodes): string
    {
        $lines = [];
        foreach ($nodes as $node) {
            $line = trim($node->nodeValue);
            if (false !== strpos($line, '|')) {
                throw new RuntimeException('Unexpected character');
            }
            $lines[] = $line;
        }

        return implode('|', $lines);
    }

    private function renderDateElement(DOMNode $node = null): ?DateTime
    {
        if (! $node) {
            return null;
        }

        $dateElement = $this->xPath->query('value', $node)->item(0);
        $date = DateTime::createFromFormat(
            'Ymd',
            $dateElement->nodeValue,
            new \DateTimeZone('UTC')
        );
        $date->setTime(0, 0, 0);

        return $date;
    }

    /**
     * @throws RuntimeException
     */
    private function renderMoneyElement(DOMNode $node): Money
    {
        $value = $this->renderSimpleTextElement($this->xPath->query('value', $node));
        $pair = explode(':', $value);
        $valueString = $pair[0];
        $currencyString = empty($pair[1]) ? 'EUR' : $pair[1];

        return $this->moneyElementRenderer->render($valueString, $currencyString);
    }

    private function renderSimpleTextElement(DOMNodeList $valueNodes): string
    {
        return trim($valueNodes->item(0)->nodeValue);
    }
}
