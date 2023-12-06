<?php

declare(strict_types=1);

namespace AqBanking\Command;

use AqBanking\Command\CheckAqBankingCommand\AqBankingNotRespondingException;
use AqBanking\Command\CheckAqBankingCommand\AqBankingVersionTooOldException;

class CheckAqBankingCommand extends AbstractCommand
{
    /**
     * @throws AqBankingVersionTooOldException
     * @throws AqBankingNotRespondingException
     */
    public function execute(): void
    {
        $this->assertAqBankingResponds();
        $this->assertAqBankingIsAppropriateVersion();
    }

    /**
     * @throws AqBankingNotRespondingException
     */
    private function assertAqBankingResponds(): void
    {
        $shellCommand = $this->pathToAqBankingCLIBinary . ' --help';
        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        if (0 !== $result->getReturnVar()) {
            throw new AqBankingNotRespondingException();
        }
    }

    /**
     * @throws AqBankingVersionTooOldException
     */
    private function assertAqBankingIsAppropriateVersion(): void
    {
        $minVersion = '5.0.24';
        $shellCommand = $this->pathToAqBankingConfigBinary . ' --vstring';
        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        if (0 !== $result->getReturnVar()) {
            throw new AqBankingVersionTooOldException(
                'Required version: ' . $minVersion . ' - present version: unknown'
            );
        }

        $versionString = $result->getOutput()[0];
        if (version_compare($versionString, $minVersion) < 0) {
            throw new AqBankingVersionTooOldException(
                'Required version: ' . $minVersion . ' - present version: ' . $versionString
            );
        }
    }
}
