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
    public function testConstruct(): void
    {
        $stream = new MemoryStream();
        $this->assertEquals(0, $stream->getSize());
        $this->assertEquals(0, $stream->tell());
        $this->assertEquals('', $stream->getContents());

        $stream = new MemoryStream('test');
        $this->assertEquals(4, $stream->getSize());
        $this->assertEquals(0, $stream->tell());
        $this->assertEquals('test', $stream->getContents());
        $stream = null;
    }
}
