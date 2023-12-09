<?php

declare(strict_types=1);

namespace AqBanking\Command;

use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\ResultAnalyzer;
use AqBanking\ExistingAccount;
use AqBanking\PinFile\PinFile;

class GetAccSepaCommand extends AbstractCommand
{
    /**
     * @throws DefectiveResultException
     */
    public function execute(ExistingAccount $account, PinFile $pinFile): void
    {
        $shellCommand =
            $this->pathToAqHBCIToolBinary
            . " --noninteractive"
            . " --acceptvalidcerts"
            . ' --pinfile=' . escapeshellcmd($pinFile->getPath())
            . ' getaccsepa'
            . ' --account=' . $account->getUniqueAccountId();

        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        $resultAnalyzer = new ResultAnalyzer();
        if ($resultAnalyzer->isDefectiveResult($result)) {
            throw new DefectiveResultException(
                'Unexpected output on getting account sepa',
                0,
                null,
                $result,
                $shellCommand
            );
        }
    }
}
