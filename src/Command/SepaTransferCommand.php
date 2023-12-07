<?php

declare(strict_types=1);

namespace AqBanking\Command;

use AqBanking\Account;
use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\ResultAnalyzer;
use AqBanking\ContextFile;
use AqBanking\PinFile\PinFile;

class SepaTransferCommand extends AbstractCommand
{
    public function __construct(
        private readonly Account $account,
        private readonly ContextFile $contextFile,
        private readonly PinFile $pinFile
    ) {
    }

    /**
     * @param string $value value to transfer "1/100:EUR"
     *
     * @throws ShellCommandExecutor\DefectiveResultException
     */
    public function execute(string $rname, string $riban, string $value, string $purpose, \DateTime $executionDate = null): void
    {
        $shellCommand = $this->getShellCommand($rname, $riban, $value, $purpose, $executionDate);
        $result = $this->getShellCommandExecutor()->execute($shellCommand);
        $resultAnalyzer = new ResultAnalyzer();
        if ($resultAnalyzer->isDefectiveResult($result)) {
            throw new DefectiveResultException(
                'Unexpected output on polling transactions',
                0,
                null,
                $result,
                $shellCommand
            );
        }
    }

    /**
     * @param string $rname remote name
     * @param string $riban remote iban
     * @param string $value value to transfer "1/100:EUR"
     * @param string $purpose purpose of the transfer
     */
    private function getShellCommand(string $rname, string $riban, string $value, string $purpose, \DateTime $executionDate = null): string
    {
        $shellCommand =
            $this->pathToAqBankingCLIBinary
            . " --noninteractive"
            . " --acceptvalidcerts"
            . " --pinfile=" . escapeshellcmd($this->pinFile->getPath())
            . " sepatransfer"
            . " --bank=" . escapeshellcmd($this->account->getBankCode()->getString())
            . " --account=" . escapeshellcmd($this->account->getAccountNumber())
            . " --ctxfile=" . escapeshellcmd($this->contextFile->getPath())
            . " --rname='" . escapeshellcmd($rname) . "'"
            . " --riban=" . escapeshellcmd($riban)
            . " --value=" . escapeshellcmd($value)
            . " --purpose='" . escapeshellcmd($purpose) . "'"
        ;

        if (null !== $executionDate) {
            $shellCommand .= " --execdate=" . $executionDate->format('Ymd');
        }

        return $shellCommand;
    }
}
