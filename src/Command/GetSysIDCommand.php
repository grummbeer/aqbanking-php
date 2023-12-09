<?php

declare(strict_types=1);

namespace AqBanking\Command;

use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\ResultAnalyzer;
use AqBanking\ExistingUser;
use AqBanking\PinFile\PinFileInterface as PinFile;

class GetSysIDCommand extends AbstractCommand
{
    /**
     * @throws ShellCommandExecutor\DefectiveResultException
     */
    public function execute(ExistingUser $user, PinFile $pinFile): void
    {
        $shellCommand =
            $this->pathToAqHBCIToolBinary
            . ' --pinfile=' . escapeshellarg($pinFile->getPath())
            . ' --noninteractive'
            . ' --acceptvalidcerts'
            . ' getsysid'
            . ' --user=' . $user->getUniqueUserId()
        ;

        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        $resultAnalyzer = new ResultAnalyzer();
        if ($resultAnalyzer->isDefectiveResult($result)) {
            throw new DefectiveResultException(
                'Unexpected output on getting user\'s accounts',
                0,
                null,
                $result,
                $shellCommand
            );
        }
    }
}
