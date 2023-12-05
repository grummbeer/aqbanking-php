<?php

namespace AqBanking;

use AqBanking\Command\ListAccountsCommand;

/**
 * Find account in existing account database
 * @package AqBanking
 */
class AccountMatcher
{
    /**
     * @var \DOMDocument
     */
    private $array;

    public function __construct($array)
    {
        $this->array = $array;
    }

    public function getExistingAccount(Account $account)
    {
        foreach ($this->array as $record) {
            if (
                $account->getBankCode()->getString() === $record[ListAccountsCommand::BANK] &&
                $account->getAccountNumber() === $record[ListAccountsCommand::NUMBER]
            ) {
                return new ExistingAccount($account, $record[ListAccountsCommand::UNIQUE_ID]);
            }
        }

        return null;
    }
}
