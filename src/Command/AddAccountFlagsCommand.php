<?php

namespace AqBanking\Command;

use AqBanking\Command\AddUserCommand\UserAlreadyExistsException;
use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\ResultAnalyzer;
use AqBanking\Account;
use AqBanking\ExistingAccount;

class AddAccountFlagsCommand extends AbstractCommand
{
    const FLAG_PREFER_CAMT_DOWNLOAD = 'preferCamtDownload';

    public function execute(ExistingAccount $account, $flags)
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
