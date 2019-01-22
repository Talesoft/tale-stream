<?php declare(strict_types=1);

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
        self::assertSame(0, $stream->getSize());
        self::assertSame(0, $stream->tell());
        self::assertSame('', $stream->getContents());

        $stream = new MemoryStream('test');
        self::assertSame(4, $stream->getSize());
        self::assertSame(0, $stream->tell());
        self::assertSame('test', $stream->getContents());
        $stream = null;
    }

    /**
     * @covers ::eof
     */
    public function testEof(): void
    {
        $stream = new MemoryStream();
        self::assertFalse($stream->eof());
        self::assertSame('', $stream->read(1));
        self::assertTrue($stream->eof());

        $stream = new MemoryStream('test');
        self::assertFalse($stream->eof());
    }
}
