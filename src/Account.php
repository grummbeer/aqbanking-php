<?php

declare(strict_types=1);

namespace AqBanking;

class Account implements AccountInterface, Arrayable
{
    public function __construct(
        private readonly BankCode $bankCode,
        private readonly string $accountNumber,
        private readonly string $accountHolderName = '',
    ) {
    }

    public function getBankCode(): BankCode
    {
        return $this->bankCode;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getAccountHolderName(): string
    {
        return $this->accountHolderName;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'bankCode' => $this->getBankCode()->getString(),
            'accountNumber' => $this->getAccountNumber(),
            'accountHolderName' => $this->getAccountHolderName(),
        ];
    }
}
