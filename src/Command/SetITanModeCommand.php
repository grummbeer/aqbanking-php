<?php

declare(strict_types=1);

namespace AqBanking\Command;

use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\ResultAnalyzer;
use AqBanking\ExistingUser;

class SetITanModeCommand extends AbstractCommand
{
    /**
     * @throws DefectiveResultException
     */
    public function execute(ExistingUser $user, string $mode): void
    {
        $shellCommand =
            $this->pathToAqHBCIToolBinary
            . ' setitanmode'
            . ' --user=' . $user->getUniqueUserId()
            . ' -m ' . escapeshellcmd($mode);

        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        $resultAnalyzer = new ResultAnalyzer();
        if ($resultAnalyzer->isDefectiveResult($result)) {
            throw new DefectiveResultException('Unexpected output on setting user itan mode', 0, null, $result, $shellCommand);
        }
    }
}
