<?php

namespace AqBanking;

/**
 * Should be greated by list users and user matcher
 *
 * @package AqBanking
 */
class ExistingAccount
{
    /**
     * @var Account
     */
    private $account;

    /**
     * @var int
     */
    private $uniqueAccountId;

    public function __construct(Account $account, int $uniqueAccountId)
    {
        $this->user = $account;
        $this->uniqueAccountId = $uniqueAccountId;
    }

    public function getUniqueAccountId()
    {
        return $this->uniqueAccountId;
    }
}
