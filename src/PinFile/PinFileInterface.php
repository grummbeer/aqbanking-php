<?php

declare(strict_types=1);

namespace AqBanking\PinFile;

interface PinFileInterface
{
    public function getFileName(): string;

    public function getPath(): string;
}
