<?php

namespace AqBanking;

use DateTime;
use Money\Money;

class Balance implements Arrayable
{
    public function __construct(
        private readonly DateTime $date,
        private readonly Money $value,
        private readonly string $type
    ) {
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): Money
    {
        return $this->value;
    }

    /**
     * @return array<string, array<int|string>|string>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'value' => [
                'amount' => $this->getValue()->getAmount(),
                'priceUnit' => 100,
                'currency' => $this->getValue()->getCurrency()->getCode(),
            ],
            'date' => $this->getDate()->format('Y-m-d'),
        ];
    }
}
