<?php

declare(strict_types=1);

namespace AqBanking\Command;

use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\ResultAnalyzer;
use AqBanking\ExistingUser;

class AddUserFlagsCommand extends AbstractCommand
{
    public const FLAG_SSL_QUIRK_IGNORE_PREMATURE_CLOSE = 'tlsIgnPrematureClose';

    /**
     * @deperacted no longer supported in AqBanking 6
     *
     * @throws ShellCommandExecutor\DefectiveResultException
     */
    public function execute(ExistingUser $user, string $flags): void
    {
        $shellCommand =
            $this->pathToAqHBCIToolBinary
            . ' adduserflags'
            . ' --user=' . $user->getUniqueUserId()
            . ' --flags=' . escapeshellcmd($flags);

        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        $resultAnalyzer = new ResultAnalyzer();
        if ($resultAnalyzer->isDefectiveResult($result)) {
            throw new DefectiveResultException('Unexpected output on setting user flags', 0, null, $result, $shellCommand);
        }
    }
}
