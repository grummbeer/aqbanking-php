<?php

declare(strict_types=1);

namespace AqBanking\Command\ShellCommandExecutor;

use Exception;

class DefectiveResultException extends Exception
{
    private ?Result $result;

    private string $shellCommand;

    public function __construct(string $message = '', int $code = 0, Exception $previous = null, Result $result = null, string $shellCommand = '')
    {
        parent::__construct(
            $message .
            " - Command: " . $shellCommand .
            " - Errors: " . implode(PHP_EOL, $result?->getErrors()),
            $code,
            $previous
        );

        $this->result = $result;
        $this->shellCommand = $shellCommand;
    }

    public function getResult(): ?Result
    {
        return $this->result;
    }

    public function getShellCommand(): string
    {
        return $this->shellCommand;
    }
}
