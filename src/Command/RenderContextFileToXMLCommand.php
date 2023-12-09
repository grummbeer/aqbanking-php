<?php

declare(strict_types=1);

namespace AqBanking\Command;

use AqBanking\ContextFile;
use DOMDocument;
use Exception;
use RuntimeException;
use SimpleXMLElement;

class RenderContextFileToXMLCommand extends AbstractCommand
{
    /**
     * @throws Exception|RuntimeException
     */
    public function execute(ContextFile $contextFile, bool $returnSimpleXml = false): DOMDocument|SimpleXMLElement
    {
        $shellCommand =
            $this->pathToAqBankingCLIBinary
            . ' export'
            . ' --ctxfile=' . escapeshellcmd($contextFile->getPath())
            . ' --transactiontype=statement' // we are only interested in statements, not notedStatement and so on
            . ' --exporter=xmldb';

        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        if (0 !== $result->getReturnVar()) {
            throw new RuntimeException(
                'AqBanking exited with errors: ' . PHP_EOL
                . implode(PHP_EOL, $result->getErrors())
            );
        }

        if ($returnSimpleXml) {
            return new SimpleXMLElement(implode(PHP_EOL, $result->getOutput()));
        }

        $domDocument = new DOMDocument();
        $domDocument->loadXML(implode(PHP_EOL, $result->getOutput()));

        return $domDocument;
    }
}
