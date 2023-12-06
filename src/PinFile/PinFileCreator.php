<?php

declare(strict_types=1);

namespace AqBanking\PinFile;

use AqBanking\User;
use InvalidArgumentException;

class PinFileCreator
{
    public function __construct(
        private readonly string $pinFileDir
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function createFile(string $pin, User $user): PinFileInterface
    {
        $pinFileDir = $this->pinFileDir;

        $this->assertIsWritableDir($pinFileDir);

        $pinFile = new PinFile($pinFileDir, $user);
        $filePath = $pinFile->getPath();
        $fileContent = $this->createFileContent(
            $pin,
            $user->getUserId(),
            $user->getBank()->getBankCode()->getString()
        );

        file_put_contents($filePath, $fileContent);

        return $pinFile;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function assertIsWritableDir(string $pinFileDir): void
    {
        if (! is_dir($pinFileDir)) {
            throw new InvalidArgumentException("PIN file dir '$pinFileDir' is not a directory");
        }
        if (! is_writable($pinFileDir)) {
            throw new InvalidArgumentException("PIN file dir '$pinFileDir' is not writable");
        }
    }

    private function createFileContent(string $pin, string $userId, string $bankCodeString): string
    {
        // The comments and line breaks seem to be mandatory for AqBanking to parse the file
        return '# This is a PIN file to be used with AqBanking' . PHP_EOL
            . '# Please insert the PINs/passwords for the users below' . PHP_EOL
            . PHP_EOL
            . '# User "' . $userId . '" at "' . $bankCodeString . '"' . PHP_EOL
            . 'PIN_' . $bankCodeString . '_' . $userId . ' = "' . $pin . '"' . PHP_EOL;
    }
}
