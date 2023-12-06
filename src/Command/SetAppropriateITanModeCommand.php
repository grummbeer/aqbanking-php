<?php

namespace AqBanking\Command;

use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\Result;
use AqBanking\HbciVersion;
use AqBanking\User;

class SetAppropriateITanModeCommand extends AbstractCommand
{
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function execute()
    {
        $this->setHbciVersion($this->determineHbciVersionToSet());
    }

    /**
     * @return HbciVersion
     * @throws DefectiveResultException
     */
    private function determineHbciVersionToSet()
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

    /**
     * @return null|HbciVersion
     */
    private function findHighestAvailableHbciVersion(Result $result)
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
    private function setHbciVersion(HbciVersion $highestVersionAvailable)
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
