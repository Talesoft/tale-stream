<?php declare(strict_types=1);

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
        self::assertSame(0, $stream->getSize());
        self::assertSame(0, $stream->tell());
        self::assertSame('', $stream->getContents());

        $stream = new TempStream('test', 3);
        self::assertSame(4, $stream->getSize());
        self::assertSame(0, $stream->tell());
        self::assertSame('test', $stream->getContents());
    }
}
