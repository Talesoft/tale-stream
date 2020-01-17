<?php declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tale\Stream;
use Tale\Stream\NullStream;

/**
 * @coversDefaultClass \Tale\Stream\NullStream
 */
class NullStreamTest extends TestCase
{
    /**
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
     * @covers ::__toString
     */
    public function testMethodReturnValues(): void
    {
        $stream = new NullStream();
        self::assertSame(0, $stream->tell());
        self::assertSame(0, $stream->getSize());
        self::assertSame('', $stream->getContents());
        $stream->close(); //It does nothing.
        self::assertNull($stream->detach());
        self::assertTrue($stream->eof());
        self::assertNull($stream->getMetadata());
        self::assertNull($stream->getMetadata('test'));
        self::assertTrue($stream->isReadable());
        self::assertTrue($stream->isSeekable());
        self::assertTrue($stream->isWritable());
        self::assertSame('', $stream->read(15));
        $stream->rewind();
        $stream->seek(15, Stream::SEEK_CURRENT);
        self::assertSame(4, $stream->write('test'));
        self::assertSame('', (string)$stream);
    }

    /**
     * @covers ::__clone
     */
    public function testCloneThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $stream = new NullStream();
        $clonedStream = clone $stream;
    }
}
