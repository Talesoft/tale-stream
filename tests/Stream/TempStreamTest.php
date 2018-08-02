<?php
declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use Tale\Stream\TempStream;

/**
 * @coversDefaultClass \Tale\Stream\TempStream
 */
class TempStreamTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::isWritable
     * @covers ::isReadable
     */
    public function testConstruct(): void
    {
        $stream = new TempStream();
        $this->assertEquals(0, $stream->getSize());
        $this->assertEquals(0, $stream->tell());
        $this->assertEquals('', $stream->getContents());

        $stream = new TempStream('test', 3);
        $this->assertEquals(4, $stream->getSize());
        $this->assertEquals(0, $stream->tell());
        $this->assertEquals('test', $stream->getContents());
    }
}
