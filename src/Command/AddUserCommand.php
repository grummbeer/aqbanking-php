<?php

declare(strict_types=1);

namespace AqBanking\Command;

use AqBanking\Command\AddUserCommand\UserAlreadyExistsException;
use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\ResultAnalyzer;
use AqBanking\User;

class AddUserCommand extends AbstractCommand
{
    public const RETURN_VAR_USER_ALREADY_EXISTS = 3;

    /**
     * @throws AddUserCommand\UserAlreadyExistsException
     * @throws ShellCommandExecutor\DefectiveResultException
     */
    public function execute(User $user): void
    {
        $shellCommand =
            $this->pathToAqHBCIToolBinary
            . ' adduser'
            . ' --username="' . escapeshellcmd($user->getUserName()) . '"'
            . ' --bank=' . escapeshellcmd($user->getBank()->getBankCode()->getString())
            . ' --user=' . escapeshellcmd($user->getUserId())
            . ' --tokentype=pintan'
            . ' --server=' . escapeshellcmd($user->getBank()->getHbciUrl());

        if ($user->getBank()->getHbciVersion()) {
            $shellCommand .= ' --hbciversion=' . escapeshellarg($user->getBank()->getHbciVersion()->getVersionNumber());
        }

        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        if (self::RETURN_VAR_USER_ALREADY_EXISTS === $result->getReturnVar()) {
            throw new UserAlreadyExistsException(implode(PHP_EOL, $result->getErrors()));
        }

        $resultAnalyzer = new ResultAnalyzer();
        if ($resultAnalyzer->isDefectiveResult($result)) {
            throw new DefectiveResultException('Unexpected output on adding a user', 0, null, $result, $shellCommand);
        }
    }
}
