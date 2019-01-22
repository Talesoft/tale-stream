<?php declare(strict_types=1);

namespace Tale\Test\Stream\Iterator;

use Tale\Stream\Iterator\ReadIterator;
use Tale\Stream\OutputStream;
use Tale\Stream\TempStream;

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
        $stream = new TempStream('test');
        $iterator = new ReadIterator($stream, 8);
        self::assertSame(8, $iterator->getChunkSize());
        self::assertSame($stream, $iterator->getStream());
        self::assertFalse($iterator->eof());
        self::assertIterator(['test'], $iterator);
        self::assertTrue($iterator->eof());
    }

    /**
     * @covers ::__construct
     * @expectedException \RuntimeException
     */
    public function testIfConstructorThrowsExceptionOnNonReadableStream(): void
    {
        $iterator = new ReadIterator(new OutputStream(), 8);
    }
}
