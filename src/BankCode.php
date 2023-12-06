<?php

declare(strict_types=1);

namespace AqBanking;

class BankCode
{
    public function __construct(
        private readonly string $bankCode
    ) {
    }

    public function getString(): string
    {
        return $this->bankCode;
    }
}
