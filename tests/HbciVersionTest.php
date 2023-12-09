<?php

declare(strict_types=1);

namespace Tests;

use AqBanking\HbciVersion;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\HbciVersion
 */
class HbciVersionTest extends TestCase
{
    public function testHbciVersion(): void
    {
        $versionNumber = '1.2.3';
        $sut = new HbciVersion(versionNumber: $versionNumber);

        $this->assertSame($versionNumber, $sut->getVersionNumber());
        $this->assertNull($sut->getMethodCode());
    }

    public function testHbciVersionMethodeCode(): void
    {
        $methodCode = '1234';

        $sut = new HbciVersion('1.2.3', $methodCode);

        $this->assertSame($methodCode, $sut->getMethodCode());
    }

    public function testCanTellIfHigherVersionThanOtherInstance(): void
    {
        $sut = new HbciVersion('1.2.3');

        $equalVersion = new HbciVersion('1.2.2');
        $this->assertTrue($sut->isHigherThan($equalVersion));

        $equalVersion = new HbciVersion('1.2.3');
        $this->assertFalse($sut->isHigherThan($equalVersion));

        $higherVersion = new HbciVersion('1.2.4');
        $this->assertFalse($sut->isHigherThan($higherVersion));
    }

    public function testIsHigherThanNull(): void
    {
        $sut = new HbciVersion('1.2.3');

        $this->assertTrue($sut->isHigherThan());
    }
}
