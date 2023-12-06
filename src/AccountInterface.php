<?php

declare(strict_types=1);

namespace AqBanking;

interface AccountInterface
{
    public function getBankCode(): BankCode;

    public function getAccountHolderName(): string;

    public function getAccountNumber(): string;
}
