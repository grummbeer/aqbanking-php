<?php

declare(strict_types=1);

namespace AqBanking\Command;

use AqBanking\Command\ShellCommandExecutor\Result;

class ShellCommandExecutor
{
    public const ERROR_REPORTING = 'error';

    public function execute(string $shellCommand): Result
    {
        $shellCommand = 'AQBANKING_LOGLEVEL=' . self::ERROR_REPORTING .
            ' GWEN_LOGLEVEL=' . self::ERROR_REPORTING .
            ' AQHBCI_LOGLEVEL=' . self::ERROR_REPORTING .
            ' LANG=C ' . $shellCommand;
        $output = [];
        $returnVar = 0;
        $tempFile = tempnam(sys_get_temp_dir(), 'aqb-');

        //        FIXME: Make a configurable log file
        //        file_put_contents('/tmp/aqbanking.log', $shellCommand . PHP_EOL, FILE_APPEND);

        exec($shellCommand . ' 2>' . $tempFile, $output, $returnVar);

        $errorOutput = file($tempFile);
        $errorOutput = array_map(function ($line) {
            return rtrim($line, "\r\n");
        }, $errorOutput);
        unlink($tempFile);

        return new Result($output, $errorOutput, $returnVar);
    }
}
