<?php

namespace AqBanking;

use AqBanking\ContentXmlRenderer\MoneyElementRenderer;
use Money\Currency;
use Money\Money;

class ContextXmlRenderer
{
    /**
     * @var \DOMDocument
     */
    private $domDocument;

    /**
     * @var \DOMXPath
     */
    private $xPath;

    /**
     * @var MoneyElementRenderer
     */
    private $moneyElementRenderer;

    public function __construct(\DOMDocument $domDocument)
    {
        $this->domDocument = $domDocument;
        $this->xPath = new \DOMXPath($domDocument);
        $this->moneyElementRenderer = new MoneyElementRenderer();
    }

    /**
     * @return Transaction[]
     */
    public function getTransactions()
    {
        $transactionNodes = $this->domDocument->getElementsByTagName('transaction');
        $transactions = array();

        foreach ($transactionNodes as $transactionNode) {
            $localBankCode = $this->renderMultiLineElement(
                $this->xPath->query('localBankCode/value', $transactionNode)
            );
            $localAccountNumber = $this->renderMultiLineElement(
                $this->xPath->query('localAccountNumber/value', $transactionNode)
            );
            $localName = $this->renderMultiLineElement($this->xPath->query('localName/value', $transactionNode));

            $remoteBankCode = $this->renderMultiLineElement(
                $this->xPath->query('remoteBankCode/value', $transactionNode)
            );
            $remoteAccountNumber = $this->renderMultiLineElement(
                $this->xPath->query('remoteAccountNumber/value', $transactionNode)
            );
            $remoteName = $this->renderMultiLineElement($this->xPath->query('remoteName/value', $transactionNode));

            $purpose = $this->renderMultiLineElement($this->xPath->query('purpose/value', $transactionNode));

            $valutaDate = $this->renderDateElement($this->xPath->query('valutaDate', $transactionNode)->item(0));
            $date = $this->renderDateElement($this->xPath->query('date', $transactionNode)->item(0));

            $value = $this->renderMoneyElement($this->xPath->query('value', $transactionNode)->item(0));

            $transactions[] = new Transaction(
                new Account(new BankCode($localBankCode), $localAccountNumber, $localName),
                new Account(new BankCode($remoteBankCode), $remoteAccountNumber, $remoteName),
                $purpose,
                $valutaDate,
                $date,
                $value
            );
        }

        return $transactions;
    }

    /**
     * @return Money
     */
    public function getBalance()
    {
        $statusNode = $this->domDocument->getElementsByTagName('bookedBalance')->item(0);

        return $this->renderMoneyElement(
            $this->xPath->query('value', $statusNode)->item(0)
        );
    }

    /**
     * @param \DOMNodeList $nodes
     * @throws \RuntimeException
     * @return string
     */
    private function renderMultiLineElement(\DOMNodeList $nodes)
    {
        $lines = array();
        foreach ($nodes as $node) {
            $line = trim($node->nodeValue);
            if (false !== strpos($line, '|')) {
                throw new \RuntimeException('Unexpected character');
            }
            $lines[] = $line;
        }

        return implode('|', $lines);
    }

    /**
     * @param \DOMNode $node
     * @param \DOMNode $node
     * @throws \RuntimeException
     * @return \DateTime
     */
    private function renderDateElement(\DOMNode $node)
    {
        $dateElement = $this->xPath->query('value', $node)->item(0);
        $date = \DateTime::createFromFormat('Ymd', $dateElement->nodeValue,
            new \DateTimeZone('UTC'));
        $date->setTime(0,0,0);
        return $date;
    }

    /**
     * @param \DOMNode $node
     * @return Money
     * @throws \Exception
     */
    private function renderMoneyElement(\DOMNode $node)
    {
        $value = $this->renderSimpleTextElement($this->xPath->query('value', $node));
        list($valueString, $currencyString) = explode(':', $value);
        return $this->moneyElementRenderer->render($valueString, $currencyString);
    }

    /**
     * @param \DOMNodeList $valueNodes
     * @return string
     */
    private function renderSimpleTextElement(\DOMNodeList $valueNodes)
    {
        return trim($valueNodes->item(0)->nodeValue);
    }
}
