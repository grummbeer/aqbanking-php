<?php

declare(strict_types=1);

namespace AqBanking;

class HbciVersion
{
    public function __construct(
        private readonly string $versionNumber,
        private readonly ?string $methodCode = null
    ) {
    }

    public function isHigherThan(?HbciVersion $hbciVersion = null): bool
    {
        if (null === $hbciVersion) {
            return true;
        }

        return (version_compare($this->versionNumber, $hbciVersion->versionNumber) > 0);
    }

    public function getMethodCode(): ?string
    {
        return $this->methodCode;
    }

    public function getVersionNumber(): string
    {
        return $this->versionNumber;
    }
}
