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
            'type' => $this->type,
            'value' => [
                'amount' => $this->value->getAmount(),
                'currency' => $this->value->getCurrency()->getCode(),
                'priceUnit' => 100,
            ],
            'date' => $this->date->format('Y-m-d'),
        ];
    }
}
