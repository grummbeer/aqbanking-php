<?php

declare(strict_types=1);

namespace AqBanking;

class User
{
    public function __construct(
        private readonly string $userId,
        private readonly string $userName,
        private readonly Bank $bank
    ) {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getBank(): Bank
    {
        return $this->bank;
    }
}
