<?php

declare(strict_types=1);

namespace AqBanking\Command\ShellCommandExecutor;

class ResultAnalyzer
{
    /**
     * @var array<string>
     */
    private array $expectedOutputRegexes = [
        '/Automatically accepting valid new certificate/',
        '/Automatically accepting certificate/',
        '/The TLS connection was non-properly terminated./', // it usually automatically restarts, so no error
        '/Unexpected tag/',
        '/To debug set environment variable/',
        '/Your bank does not send an opening saldo/',
        '/Bank data for KtoBlzCheck not found/',
        '/Executing Jobs: Started\./',
        '/A TLS packet with unexpected length was received\./',
        // The following happens when using flag FLAG_SSL_QUIRK_IGNORE_PREMATURE_CLOSE for some banks
        '/Detected premature disconnect by server \(violates specs!\), ignoring\./',
        '/The TLS connection was non-properly terminated./',
        '/Bad IBAN \(country code not in upper case\)/',
        '/Adding flags/',
        '/You may see some messages like "Job not supported with this account" below, that\'s are okay, please ignore/',
        '/not supported with this account/',
        '/Account exists, modifying/',
        '/Account is new, adding/',
        '/Depot job not found/',
        '/Adding supported CAMT format/',
        '/Adding supported CAMT form/',
        '/Path "Ustrd" not found/',
        '/Adding matching profile/',
        '/Handling user/',
        '/Writing account spec/',
        '/===== Executing Jobs =====/',
        '/===== Getting Certificate =====/',
        '/Handling user/',
        '/RXH-encrypting message/',
        '/No AqBanking config folder found at/',
        '/There is no old settings folder, need initial setup/',
        '/Account is new, adding/',
        '/^  .*$/', // everything starting with a space belongs to a previous message and is not an error (hopefully)
        '/Selecting PAIN format.*$/',
    ];

    public function isDefectiveResult(Result $result): bool
    {
        if (0 !== $result->getReturnVar()) {
            return true;
        }
        if ($this->resultHasErrors($result)) {
            return true;
        }
        return false;
    }

    private function resultHasErrors(Result $result): bool
    {
        if (1 === \count($result->getErrors()) && str_contains($result->getErrors()[0], 'accepting valid new certificate')) {
            // When calling getsysid with wrong PIN, we don't get any error message.
            // The only significant aspect of the error is that the output is just one line with
            // "accepting valid new certificate"
            return true;
        }
        foreach ($result->getErrors() as $line) {
            if ($this->isErrorMessage($line)) {
                return true;
            }
        }
        return false;
    }

    private function isErrorMessage(string $line): bool
    {
        foreach ($this->expectedOutputRegexes as $regex) {
            if (preg_match($regex, $line)) {
                return false;
            }
        }
        return true;
    }
}
