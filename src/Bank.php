<?php

declare(strict_types=1);

namespace AqBanking;

class Bank
{
    public function __construct(
        private readonly BankCode $bankCode,
        private readonly string $hbciUrl,
        private readonly ?HbciVersion $hbciVersion = null
    ) {
    }

    public function getBankCode(): BankCode
    {
        return $this->bankCode;
    }

    public function getHbciUrl(): string
    {
        return $this->hbciUrl;
    }

    public function getHbciVersion(): ?HbciVersion
    {
        return $this->hbciVersion;
    }
}
