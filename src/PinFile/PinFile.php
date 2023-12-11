<?php

declare(strict_types=1);

namespace AqBanking\PinFile;

use AqBanking\User;

class PinFile implements PinFileInterface
{
    public function __construct(
        private readonly string $dir,
        private readonly User $user
    ) {
    }

    public function getFileName(): string
    {
        return sprintf(
            'pinfile_%s_%s',
            $this->user->getBank()->getBankCode()->getString(),
            $this->user->getUserId()
        );
    }

    public function getPath(): string
    {
        return rtrim($this->dir, '/') . '/' . $this->getFileName();
    }
}
