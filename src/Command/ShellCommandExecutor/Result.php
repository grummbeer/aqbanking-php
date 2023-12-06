<?php

declare(strict_types=1);

namespace AqBanking\Command\ShellCommandExecutor;

class Result
{
    /**
     * @param array<string> $output
     * @param array<string> $errors
     */
    public function __construct(
        private readonly array $output,
        private readonly array $errors,
        private readonly int $returnVar
    ) {
    }

    /**
     * @return array<string>
     */
    public function getOutput(): array
    {
        return $this->output;
    }

    /**
     * @return array<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getReturnVar(): int
    {
        return $this->returnVar;
    }
}
