<?php

declare(strict_types=1);

namespace AqBanking\Command;

use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\ResultAnalyzer;
use AqBanking\ExistingAccount;

class AddAccountFlagsCommand extends AbstractCommand
{
    public const FLAG_PREFER_CAMT_DOWNLOAD = 'preferCamtDownload';

    /**
     * @throws DefectiveResultException
     */
    public function execute(ExistingAccount $account, string $flags): void
    {
        $shellCommand =
            $this->pathToAqHBCIToolBinary
            . ' addaccountflags'
            . ' --account=' . $account->getUniqueAccountId()
            . ' --flags=' . escapeshellcmd($flags);

        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        $resultAnalyzer = new ResultAnalyzer();
        if ($resultAnalyzer->isDefectiveResult($result)) {
            throw new DefectiveResultException('Unexpected output on setting account flags', 0, null, $result, $shellCommand);
        }
    }
}
