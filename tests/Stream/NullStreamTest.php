<?php
declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use Tale\Stream;
use Tale\Stream\NullStream;
use Tale\Stream\StandardErrorStream;

/**
 * @coversDefaultClass \Tale\Stream\StandardErrorStream
 */
class NullStreamTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::tell
     * @covers ::getSize
     * @covers ::getContents
     * @covers ::close
     * @covers ::detach
     * @covers ::eof
     * @covers ::getMetadata
     * @covers ::isReadable
     * @covers ::isSeekable
     * @covers ::isWritable
     * @covers ::read
     * @covers ::rewind
     * @covers ::seek
     * @covers ::write
     */
    public function testMethodReturnValues(): void
    {
        $stream = new NullStream();
        $this->assertEquals(0, $stream->tell());
        $this->assertEquals(0, $stream->getSize());
        $this->assertEquals('', $stream->getContents());
        $stream->close(); //It does nothing.
        $this->assertNull($stream->detach());
        $this->assertTrue($stream->eof());
        $this->assertNull($stream->getMetadata());
        $this->assertNull($stream->getMetadata('test'));
        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isSeekable());
        $this->assertTrue($stream->isWritable());
        $this->assertFalse($stream->read(15));
        $this->assertTrue($stream->rewind());
        $this->assertTrue($stream->seek(15, Stream::SEEK_CURRENT));
        $this->assertEquals(4, $stream->write('test'));
    }
}
