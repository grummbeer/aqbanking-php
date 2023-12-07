<?php

declare(strict_types=1);

namespace AqBanking\Command;

use AqBanking\AccountInterface as Account;
use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\ResultAnalyzer;
use AqBanking\ContextFile;
use AqBanking\PinFile\PinFileInterface as PinFile;
use DateTime;

class RequestCommand extends AbstractCommand
{
    public function __construct(
        private readonly Account $account,
        private readonly ContextFile $contextFile,
        private readonly PinFile $pinFile
    ) {
    }

    /**
     * @throws ShellCommandExecutor\DefectiveResultException
     */
    public function execute(DateTime $fromDate = null): void
    {
        $shellCommand = $this->getShellCommand($fromDate);
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

    private function getShellCommand(DateTime $fromDate = null): string
    {
        $shellCommand =
            $this->pathToAqBankingCLIBinary
            . " --noninteractive"
            . " --acceptvalidcerts"
            . " --pinfile=" . escapeshellcmd($this->pinFile->getPath())
            . " request"
            . " --bank=" . escapeshellcmd($this->account->getBankCode()->getString())
            . " --account=" . escapeshellcmd($this->account->getAccountNumber())
            . " --ctxfile=" . escapeshellcmd($this->contextFile->getPath())
            . " --transactions"
            . " --balance"
            // TODO: Standing orders and dated transfers are not supported by some account types
            //. " --sto"     // standing orders
            //. " --dated"   // dated transfers
        ;

        if (null !== $fromDate) {
            $shellCommand .= " --fromdate=" . $fromDate->format('Ymd');
        }

        return $shellCommand;
    }
}
