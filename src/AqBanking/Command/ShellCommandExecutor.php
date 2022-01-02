<?php

namespace AqBanking\Command;

use AqBanking\Command\ShellCommandExecutor\Result;

class ShellCommandExecutor
{
    const ERROR_REPORTING = 'error';

    private $pid = -1;

    public function execute($shellCommand)
    {
        $shellCommand = 'AQBANKING_LOGLEVEL=' . self::ERROR_REPORTING .
            ' GWEN_LOGLEVEL=' . self::ERROR_REPORTING .
            ' AQHBCI_LOGLEVEL=' . self::ERROR_REPORTING .
            ' LANG=C ' . $shellCommand;
        $output = array();
        $returnVar = null;
        $tempFile = tempnam(sys_get_temp_dir(), 'aqb-');

        $this->pid = exec($shellCommand . ' 2>' . $tempFile ." & echo $!;", $output, $returnVar);

        $errorOutput = file($tempFile);
        $errorOutput = array_map(function ($line) {
            return rtrim($line, "\r\n");
        }, $errorOutput);
        unlink($tempFile);

        return new Result($output, $errorOutput, $returnVar);
    }


    public function isRunning()
    {
        if (exec(printf('ps -p %s -o "pid=" >/dev/null 2>&1', $this->pid))) {
            return false;
        } else {
            return true;
        }
    }

}
