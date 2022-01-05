<?php

namespace AqBanking;

class Account implements AccountInterface, Arrayable
{
    /**
     * @var BankCode
     */
    private $bankCode;

    /**
     * @var string
     */
    private $accountNumber;

    /**
     * @param BankCode $bankCode
     * @param string $accountNumber
     * @return \AqBanking\Account
     */
    public function __construct(BankCode $bankCode, $accountNumber)
    {
        $this->bankCode = $bankCode;
        $this->accountNumber = $accountNumber;
    }

    /**
     * @return BankCode
     */
    public function getBankCode()
    {
        return $this->bankCode;
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    public function toArray()
    {
        return [
            'bankCode' => $this->getBankCode()->getString(),
            'accountNumber' => $this->getAccountNumber()
        ];
    }
}
