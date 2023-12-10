<?php

declare(strict_types=1);

namespace Tests\PinFile;

use AqBanking\Bank;
use AqBanking\BankCode;
use AqBanking\HbciVersion;
use AqBanking\PinFile\PinFile;
use AqBanking\User;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\PinFile\PinFile
 * @uses \AqBanking\Bank
 * @uses \AqBanking\BankCode
 * @uses \AqBanking\HbciVersion
 * @uses \AqBanking\User
 */
class PinFileTest extends TestCase
{
    private static string $userId = '1';

    private static string $bankCode = '50050010';

    /**
     * @dataProvider samples
     */
    public function testPinFile(string $dir, User $user, string $expectedFileName, string $expectedPath): void
    {
        $sut = new PinFile(
            dir: $dir,
            user: $user
        );

        $this->assertSame($expectedFileName, $sut->getFileName());
        $this->assertSame($expectedPath, $sut->getPath());
    }

    public static function samples(): array
    {
        $user = new User(
            userId: self::$userId,
            userName: 'Max Mustermann',
            bank: new Bank(
                bankCode: new BankCode(self::$bankCode),
                hbciUrl: 'https://fints.bank.de/fints',
                hbciVersion: new HbciVersion('1.2.3'),
            )
        );

        $expectedFileName = 'pinfile_' . self::$bankCode . '_' . self::$userId;

        return [
            'trailing slash' => [
                './',
                $user,
                $expectedFileName,
                './' . $expectedFileName,
            ],
            'no trailing slash' => [
                '/home',
                $user,
                $expectedFileName,
                '/home/' . $expectedFileName,
            ],
        ];
    }
}
