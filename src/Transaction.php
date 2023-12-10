<?php

declare(strict_types=1);

namespace AqBanking;

use DateTime;
use Money\Money;

class Transaction implements Arrayable
{
    public function __construct(
        private readonly Account $localAccount,
        private readonly Account $remoteAccount,
        private readonly string $type,
        private readonly string $purpose,
        private readonly ?DateTime $valutaDate = null,
        private readonly DateTime $date,
        private readonly Money $value,
        private readonly string $primaNota,
        private readonly string $customerReference
    ) {
    }

    public function getLocalAccount(): Account
    {
        return $this->localAccount;
    }

    public function getRemoteAccount(): Account
    {
        return $this->remoteAccount;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getPurpose(): string
    {
        return $this->purpose;
    }

    public function getValue(): Money
    {
        return $this->value;
    }

    public function getValutaDate(): ?DateTime
    {
        return $this->valutaDate;
    }

    public function getPrimaNota(): string
    {
        return $this->primaNota;
    }

    public function getCustomerReference(): string
    {
        return $this->customerReference;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'date' => $this->getDate()->format('Y-m-d'),
            'localAccount' => $this->getLocalAccount()->toArray(),
            'purpose' => $this->getPurpose(),
            'remoteAccount' => $this->getRemoteAccount()->toArray(),
            'type' => $this->getType(),
            'value' => [
                'amount' => $this->getValue()->getAmount(),
                'currency' => $this->getValue()->getCurrency()->getCode(),
                'priceUnit' => 100,
            ],
            'valutaDate' => $this->getValutaDate()?->format('Y-m-d'),
            'primaNota' => $this->getPrimaNota(),
            'customerReference' => $this->getCustomerReference(),
        ];
    }
}
