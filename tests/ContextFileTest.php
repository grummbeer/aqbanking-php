<?php

declare(strict_types=1);

namespace Tests;

use AqBanking\ContextFile;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AqBanking\ContextFile
 */
class ContextFileTest extends TestCase
{
    public function testContextFile(): void
    {
        $path = '/path/to/contextFile';
        $sut = new ContextFile($path);
        $this->assertSame($path, $sut->getPath());
    }
}
