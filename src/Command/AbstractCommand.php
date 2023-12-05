<?php

namespace AqBanking\Command;

abstract class AbstractCommand
{
    /**
     * @var string
     */
    protected $pathToAqBankingCLIBinary = 'aqbanking-cli';

    /**
     * @var string
     */
    protected $pathToAqBankingConfigBinary = 'aqbanking-config';

    /**
     * @var string
     */
    protected $pathToAqHBCIToolBinary = 'aqhbci-tool4';

    /**
     * @var null|ShellCommandExecutor
     */
    private $shellCommandExecutor = null;

    public function setShellCommandExecutor(ShellCommandExecutor $shellCommandExecutor)
    {
        $this->shellCommandExecutor = $shellCommandExecutor;
    }

    /**
     * @param string $binaryPath
     */
    public function setPathToAqBankingCLIBinary($binaryPath)
    {
        $this->pathToAqBankingCLIBinary = $binaryPath;
    }

    /**
     * @param string $pathToAqBankingConfigBinary
     */
    public function setPathToAqBankingConfigBinary($pathToAqBankingConfigBinary)
    {
        $this->pathToAqBankingConfigBinary = $pathToAqBankingConfigBinary;
    }

    /**
     * @param string $pathToAqHBCIToolBinary
     */
    public function setPathToAqHBCIToolBinary($pathToAqHBCIToolBinary)
    {
        $this->pathToAqHBCIToolBinary = $pathToAqHBCIToolBinary;
    }

    /**
     * @return ShellCommandExecutor
     */
    protected function getShellCommandExecutor()
    {
        if (null === $this->shellCommandExecutor) {
            $this->shellCommandExecutor = new ShellCommandExecutor();
        }

        return $this->shellCommandExecutor;
    }
}
