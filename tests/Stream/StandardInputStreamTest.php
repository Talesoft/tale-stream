<?php
declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use Tale\Stream\StandardInputStream;

/**
 * @coversDefaultClass \Tale\Stream\StandardInputStream
 */
class StandardInputStreamTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::isReadable
     * @covers ::isWritable
     */
    public function testConstruct(): void
    {
        $stream = new StandardInputStream();
        $this->assertTrue($stream->isReadable());
        $this->assertFalse($stream->isWritable());
        $stream = null;
    }
}
