<?php

declare(strict_types=1);

namespace AqBanking;

/**
 * Should be created by list users and user matcher
 */
class ExistingAccount
{
    public function __construct(
        private readonly Account $account,
        private readonly int $uniqueAccountId
    ) {
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function getUniqueAccountId(): int
    {
        return $this->uniqueAccountId;
    }
}
