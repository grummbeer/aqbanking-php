<?php

namespace AqBanking\Command;

use AqBanking\Command\AddUserCommand\UserAlreadyExistsException;
use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\ResultAnalyzer;
use AqBanking\User;

class ListAccountsCommand extends AbstractCommand
{
    const RETURN_VAR_NOT_FOUND = 4;

    const BANK = 'bank';
    const NUMBER = 'number';
    const UNIQUE_ID = 'uniqueId';
    /**
     * @return array
     */
    public function execute()
    {
        $shellCommand =
            $this->pathToAqHBCIToolBinary
            . ' listaccounts'
            . ' -v';

        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        if ($result->getReturnVar() !== 0) {
            throw new \RuntimeException(
                'AqBanking exited with errors: ' . PHP_EOL
                . implode(PHP_EOL, $result->getErrors())
            );
        }

        $accounts = [];
        foreach($result->getOutput() as $line) {
            $parsed = sscanf($line, 'Account %d: Bank: %s Account Number: %s  SubAccountId: %s  Account Type: %s LocalUniqueId: %d' );
            $accounts[] = [
                self::BANK => $parsed[1],
                self::NUMBER => $parsed[2],
                self::UNIQUE_ID => $parsed[5],
            ];
        }

        return $accounts;
    }
}
