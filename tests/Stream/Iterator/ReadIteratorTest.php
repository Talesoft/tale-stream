<?php
declare(strict_types=1);

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
     * @covers ::setChunkSize
     * @covers ::eof
     * @covers ::rewind
     * @covers ::getIterator
     */
    public function testConstruct(): void
    {
        $stream = new TempStream('test');
        $iterator = new ReadIterator($stream, 8);
        $this->assertEquals(8, $iterator->getChunkSize());
        $this->assertSame($stream, $iterator->getStream());
        $this->assertFalse($iterator->eof());
        $this->assertIterator($iterator, ['test']);
        $this->assertTrue($iterator->eof());

        $iterator->rewind();

        $this->assertSame($iterator, $iterator->setChunkSize(3));
        $this->assertEquals(3, $iterator->getChunkSize());
        $this->assertIterator($iterator, ['tes', 't']);
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
