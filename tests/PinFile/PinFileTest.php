<?php

declare(strict_types=1);

namespace PinFile;

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
    public function testPinFile(): void
    {
        $userId = '1';
        $bankCode = '50050010';

        $user = new User(
            userId: $userId,
            userName: 'Max Mustermann',
            bank: new Bank(
                bankCode: new BankCode($bankCode),
                hbciUrl: 'https://fints.bank.de/fints',
                hbciVersion: new HbciVersion('1.2.3'),
            )
        );

        $dir = sys_get_temp_dir();
        $sut = new PinFile(
            dir: $dir,
            user: $user
        );

        $expectedFileName = 'pinfile_' . $bankCode . '_' . $userId;

        $this->assertSame($expectedFileName, $sut->getFileName());
        $this->assertSame(implode('/', [$dir, $expectedFileName]), $sut->getPath());
    }
}
