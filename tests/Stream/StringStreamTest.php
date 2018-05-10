<?php
declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use Tale\Stream\StringStream;

/**
 * @coversDefaultClass \Tale\Stream\StringStream
 */
class StringStreamTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::isWritable
     * @covers ::__toString
     */
    public function testConstruct()
    {
        $stream = new StringStream(null, 'wb');
        $this->assertTrue($stream->isWritable());
        $this->assertTrue($stream->isReadable());

        $stream = new StringStream(null, 'rb');
        $this->assertTrue($stream->isReadable());
        $this->assertFalse($stream->isWritable());

        $stream = new StringStream(null, 'rb+');
        $this->assertTrue($stream->isWritable());
        $this->assertTrue($stream->isReadable());

        $stream = new StringStream('This is a test string');
        $this->assertEquals('This is a test string', (string)$stream);
        $this->assertTrue($stream->isWritable());
        $this->assertTrue($stream->isReadable());
    }
}
