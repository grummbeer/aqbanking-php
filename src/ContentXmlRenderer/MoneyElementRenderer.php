<?php

declare(strict_types=1);

namespace AqBanking\ContentXmlRenderer;

use AqBanking\RuntimeException;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;

class MoneyElementRenderer
{
    /**
     * @throws RuntimeException
     */
    public function render(string $value, string $currencyString): Money
    {
        if ('' === $currencyString) {
            $currencyString = 'EUR';
        }
        $currency = new Currency($currencyString);

        if (false === (new ISOCurrencies())->contains($currency)) {
            throw new RuntimeException("Unknown currency input '$currencyString'");
        }

        return new Money($this->normalizeAmount($value), $currency);
    }

    /**
     * @see https://github.com/janunger/aqbanking-php/issues/1
     *
     * @FIXME You can't rely on floating point numbers. Use bcmath instead.
     *        see https://0.30000000000000004.com/
     *        php -r "var_dump(.1 + .2);" // float(0.30000000000000004)
     *
     * @throws RuntimeException
     */
    private function normalizeAmount(string $value): int
    {
        list($amount, $divisor) = $this->extractAmountAndDivisorAsString($value);

        $multiplier = 100 / (int) $divisor;
        $normalizedAmount = (int) $amount * $multiplier;

        if ($this->isNormalizedValueBiassed($normalizedAmount)) {
            throw new RuntimeException(
                "Biassed rendering result '$normalizedAmount' from amount '$amount' and divisor '$divisor'"
            );
        }

        return (int) $normalizedAmount;
    }

    /**
     * @throws RuntimeException
     *
     * @return string[]
     */
    private function extractAmountAndDivisorAsString(string $amountString): array
    {
        $matches = [];
        if (preg_match('/^(?P<amount>(-){0,1}\d+)\/(?P<divisor>1(0)*)$/', $amountString, $matches)) {
            $amount = $matches['amount'];
            $divisor = $matches['divisor'];

            return [$amount, $divisor];
        }

        $matches = [];
        if (preg_match('/^(?P<amount>(-){0,1}\d+)$/', $amountString, $matches)) {
            $amount = $matches['amount'];

            return [$amount, '1'];
        }

        throw new RuntimeException("Unexpected amount input '$amountString'");
    }

    private function isNormalizedValueBiassed(float|int $normalizedAmount): bool
    {
        return 0.00 !== fmod($normalizedAmount, 1);
    }
}
