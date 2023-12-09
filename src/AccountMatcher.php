<?php

declare(strict_types=1);

namespace AqBanking;

use AqBanking\Command\ListAccountsCommand;

/**
 * Find account in existing account database
 */
class AccountMatcher
{
    public function __construct(
        private readonly array $existingAccounts
    ) {
    }

    public function getExistingAccount(Account $account): ?ExistingAccount
    {
        foreach ($this->existingAccounts as $record) {
            if (
                $account->getBankCode()->getString() === $record[ListAccountsCommand::BANK] &&
                $account->getAccountNumber() === $record[ListAccountsCommand::NUMBER]
            ) {
                return new ExistingAccount($account, (int) $record[ListAccountsCommand::UNIQUE_ID]);
            }
        }

        return null;
    }
}
