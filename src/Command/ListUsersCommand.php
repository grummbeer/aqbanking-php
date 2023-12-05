<?php

namespace AqBanking\Command;

class ListUsersCommand extends AbstractCommand
{
    public const RETURN_VAR_NOT_FOUND = 4;

    /**
     * @return \DOMDocument|null
     */
    public function execute()
    {
        $shellCommand =
            $this->pathToAqHBCIToolBinary
            . ' listusers'
            . ' --xml';

        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        if (4 === $result->getReturnVar()) {
            return null;
        }

        if (0 !== $result->getReturnVar()) {
            throw new \RuntimeException(
                'AqBanking exited with errors: ' . PHP_EOL
                . implode(PHP_EOL, $result->getErrors())
            );
        }

        $domDocument = new \DOMDocument();
        $domDocument->loadXML(implode(PHP_EOL, $result->getOutput()));

        return $domDocument;
    }
}
