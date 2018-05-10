<?php
declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use Tale\Stream\MemoryStream;

/**
 * @coversDefaultClass \Tale\Stream\MemoryStream
 */
class MemoryStreamTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::isWritable
     * @covers ::isReadable
     */
    public function testConstruct()
    {
        $stream = new MemoryStream('wb');
        $this->assertTrue($stream->isWritable());
        $this->assertTrue($stream->isReadable());

        $stream = new MemoryStream('rb');
        $this->assertTrue($stream->isReadable());
        $this->assertFalse($stream->isWritable());

        $stream = new MemoryStream('rb+');
        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isWritable());
    }
}
