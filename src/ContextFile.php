<?php

declare(strict_types=1);

namespace AqBanking;

class ContextFile
{
    public function __construct(
        private readonly string $path
    ) {
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
