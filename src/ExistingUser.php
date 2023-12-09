<?php

declare(strict_types=1);

namespace AqBanking;

/**
 * Should be created by list users and user matcher
 */
class ExistingUser
{
    public function __construct(
        private readonly User $user,
        private readonly int $uniqueUserId
    ) {
    }

    public function getUniqueUserId(): int
    {
        return $this->uniqueUserId;
    }

    public function getUserId(): string
    {
        return $this->user->getUserId();
    }

    public function getUserName(): string
    {
        return $this->user->getUserName();
    }

    public function getBank(): Bank
    {
        return $this->user->getBank();
    }
}
