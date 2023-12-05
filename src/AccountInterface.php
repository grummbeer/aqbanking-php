<?php

namespace AqBanking;

interface AccountInterface
{
    public function getBankCode(): BankCode;
    public function getAccountHolderName(): string;
    public function getAccountNumber(): string;
    public function getIban(): string;
}
