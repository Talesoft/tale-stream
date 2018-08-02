<?php
declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use Tale\Stream\OutputStream;

/**
 * @coversDefaultClass \Tale\Stream\OutputStream
 */
class OutputStreamTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::isWritable
     * @covers ::isReadable
     */
    public function testConstruct(): void
    {
        $stream = new OutputStream();
        $this->assertTrue($stream->isWritable());
        $this->assertFalse($stream->isReadable());
        $stream = null;
    }
}
