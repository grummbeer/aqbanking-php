<?php

declare(strict_types=1);

namespace AqBanking\Command;

use RuntimeException;

class ListAccountsCommand extends AbstractCommand
{
    public const BANK = 'bank';

    public const NUMBER = 'number';

    public const UNIQUE_ID = 'uniqueId';

    public function execute(): array
    {
        $shellCommand =
            $this->pathToAqHBCIToolBinary
            . ' listaccounts'
            . ' -v';

        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        if (0 !== $result->getReturnVar()) {
            throw new RuntimeException(
                'AqBanking exited with errors: ' . PHP_EOL
                . implode(PHP_EOL, $result->getErrors())
            );
        }

        $accounts = [];
        foreach ($result->getOutput() as $line) {
            $parsed = sscanf($line, 'Account %d: Bank: %s Account Number: %s  SubAccountId: %s  Account Type: %s LocalUniqueId: %d');
            $accounts[] = [
                self::BANK => $parsed[1],
                self::NUMBER => $parsed[2],
                self::UNIQUE_ID => $parsed[5],
            ];
        }

        return $accounts;
    }
}
