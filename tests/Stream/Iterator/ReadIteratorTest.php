<?php declare(strict_types=1);

namespace Tale\Test\Stream\Iterator;

use Tale\Stream;
use Tale\Stream\Exception\NotReadableException;
use Tale\Stream\Iterator\ReadIterator;

/**
 * @coversDefaultClass \Tale\Stream\Iterator\ReadIterator
 */
class ReadIteratorTest extends AbstractIteratorTest
{
    /**
     * @covers ::__construct
     * @covers ::getStream
     * @covers ::getChunkSize
     * @covers ::eof
     * @covers ::getIterator
     */
    public function testConstruct(): void
    {
        $stream = Stream::createTempStream('test');
        $iterator = new ReadIterator($stream, 8);
        self::assertSame(8, $iterator->getChunkSize());
        self::assertSame($stream, $iterator->getStream());
        self::assertFalse($iterator->eof());
        self::assertIterator(['test'], $iterator);
        self::assertTrue($iterator->eof());
    }

    /**
     * @covers ::__construct
     */
    public function testIfConstructorThrowsExceptionOnNonReadableStream(): void
    {
        $this->expectException(NotReadableException::class);
        $iterator = new ReadIterator(Stream::createOutputStream(), 8);
    }
}
