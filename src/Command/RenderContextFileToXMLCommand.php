<?php

namespace AqBanking\Command;

use AqBanking\ContextFile;

class RenderContextFileToXMLCommand extends AbstractCommand
{
    /**
     * @param ContextFile $contextFile
     * @return \DOMDocument
     * @throws \RuntimeException
     */
    public function execute(ContextFile $contextFile, bool $returnSimpleXml = false)
    {
        $shellCommand =
            $this->pathToAqBankingCLIBinary
            . ' export'
            . ' --ctxfile=' . escapeshellcmd($contextFile->getPath())
            . ' --transactiontype=statement' // we are only interested in statements, not notedStatement and so on
            . ' --exporter=xmldb';

        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        if ($result->getReturnVar() !== 0) {
            throw new \RuntimeException(
                'AqBanking exited with errors: ' . PHP_EOL
                . implode(PHP_EOL, $result->getErrors())
            );
        }

        if($returnSimpleXml) {
            return new \SimpleXMLElement(implode(PHP_EOL, $result->getOutput()));
        }

        $domDocument = new \DOMDocument();
        $domDocument->loadXML(implode(PHP_EOL, $result->getOutput()));

        return $domDocument;
    }
}
