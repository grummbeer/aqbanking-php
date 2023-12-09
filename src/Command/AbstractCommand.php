<?php

declare(strict_types=1);

namespace AqBanking\Command;

abstract class AbstractCommand
{
    protected string $pathToAqBankingCLIBinary = 'aqbanking-cli';

    protected string $pathToAqBankingConfigBinary = 'aqbanking-config';

    protected string $pathToAqHBCIToolBinary = 'aqhbci-tool4';

    private ?ShellCommandExecutor $shellCommandExecutor = null;

    public function setShellCommandExecutor(ShellCommandExecutor $shellCommandExecutor): void
    {
        $this->shellCommandExecutor = $shellCommandExecutor;
    }

    public function setPathToAqBankingCLIBinary(string $binaryPath): void
    {
        $this->pathToAqBankingCLIBinary = $binaryPath;
    }

    public function setPathToAqBankingConfigBinary(string $pathToAqBankingConfigBinary): void
    {
        $this->pathToAqBankingConfigBinary = $pathToAqBankingConfigBinary;
    }

    public function setPathToAqHBCIToolBinary(string $pathToAqHBCIToolBinary): void
    {
        $this->pathToAqHBCIToolBinary = $pathToAqHBCIToolBinary;
    }

    protected function getShellCommandExecutor(): ShellCommandExecutor
    {
        if (null === $this->shellCommandExecutor) {
            $this->shellCommandExecutor = new ShellCommandExecutor();
        }

        return $this->shellCommandExecutor;
    }
}
