<?php

declare(strict_types=1);

namespace AqBanking\Command;

use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\Result;
use AqBanking\HbciVersion;
use AqBanking\User;

class SetAppropriateITanModeCommand extends AbstractCommand
{
    public function __construct(
        private readonly User $user
    ) {
    }

    /**
     * @throws DefectiveResultException
     */
    public function execute(): void
    {
        $this->setHbciVersion($this->determineHbciVersionToSet());
    }

    /**
     * @throws DefectiveResultException
     */
    private function determineHbciVersionToSet(): HbciVersion
    {
        $shellCommand =
            $this->pathToAqHBCIToolBinary .
            " listitanmodes" .
            " --bank=" . escapeshellcmd($this->user->getBank()->getBankCode()->getString()) .
            " --user=" . escapeshellcmd($this->user->getUserId());
        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        $requiredHbciVersion = $this->user->getBank()->getHbciVersion();
        $highestVersionAvailable = $this->findHighestAvailableHbciVersion($result);

        if (! $highestVersionAvailable) {
            throw new DefectiveResultException(
                'AqBanking could not find any available HBCI version',
                0,
                null,
                $result,
                $shellCommand
            );
        }
        if ($requiredHbciVersion && $requiredHbciVersion->isHigherThan($highestVersionAvailable)) {
            throw new DefectiveResultException(
                'AqBanking could not find an available HBCI version that is high enough',
                0,
                null,
                $result,
                $shellCommand
            );
        }

        return $highestVersionAvailable;
    }

    private function findHighestAvailableHbciVersion(Result $result): ?HbciVersion
    {
        $highestVersionAvailable = null;

        foreach ($result->getOutput() as $line) {
            $matches = [];
            $regex = '/^- (?P<code>\d+) \(.+\/(V(?P<version>\d+))\/.+\).*\[available( and selected)?\]$/';
            if (! preg_match($regex, $line, $matches)) {
                continue;
            }
            $version = new HbciVersion($matches['version'], $matches['code']);
            if ($version->isHigherThan($highestVersionAvailable)) {
                $highestVersionAvailable = $version;
            }
        }

        return $highestVersionAvailable;
    }

    /**
     * @throws DefectiveResultException
     */
    private function setHbciVersion(HbciVersion $highestVersionAvailable): void
    {
        $shellCommand =
            $this->pathToAqHBCIToolBinary .
            " setitanmode" .
            " --bank=" . escapeshellcmd($this->user->getBank()->getBankCode()->getString()) .
            " --user=" . escapeshellcmd($this->user->getUserId()) .
            " --method=" . escapeshellcmd($highestVersionAvailable->getMethodCode());

        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        if (0 !== $result->getReturnVar() || \count($result->getErrors()) > 0) {
            throw new DefectiveResultException(
                'Unexpected result on setting the user\'s HBCI version',
                0,
                null,
                $result,
                $shellCommand
            );
        }
    }
}
