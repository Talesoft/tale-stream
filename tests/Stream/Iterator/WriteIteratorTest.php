<?php
declare(strict_types=1);

namespace Tale\Test\Stream\Iterator;

use CallbackFilterIterator;
use IteratorIterator;
use Tale\Stream\Exception\NotReadableException;
use Tale\Stream\InputStream;
use Tale\Stream\Iterator\LineIterator;
use Tale\Stream\Iterator\WriteIterator;
use Tale\Stream\MemoryStream;

/**
 * @coversDefaultClass \Tale\Stream\Iterator\WriteIterator
 */
class WriteIteratorTest extends AbstractIteratorTest
{
    /**
     * @covers ::__construct
     * @covers ::getStream
     * @covers ::getSourceIterator
     * @covers ::rewind
     * @covers ::getIterator
     */
    public function testConstruct(): void
    {
        $inputStream = new MemoryStream("ab\ncd\nde\ng");

        //Use a LineIterator to cleanly read lines
        $reader = new LineIterator($inputStream);

        //Will filter all lines that match "de"
        $filteredReader = new CallbackFilterIterator($reader->getIterator(), function (string $line) {
            return $line !== 'de';
        });

        //Will add "\n" to all lines
        $addLfModifier = new class($filteredReader) extends IteratorIterator
        {
            public function current(): string
            {
                return parent::current()."\n";
            }
        };

        $outputStream = new MemoryStream();

        $pipe = new WriteIterator($outputStream, $addLfModifier);
        $this->assertSame($outputStream, $pipe->getStream());
        $this->assertSame($addLfModifier, $pipe->getSourceIterator());
        $writtenBytes = iterator_to_array($pipe); //The actual piping process, chunk-by-chunk
        $this->assertSame([3, 3, 2], $writtenBytes);

        $this->assertEquals("ab\ncd\ng\n", (string)$outputStream); //"ab\ncd\ng"

        $pipe->rewind();
    }

    /**
     * @covers ::__construct
     * @expectedException \RuntimeException
     */
    public function testIfConstructThrowsExceptionWhenStreamIsNotWritable(): void
    {
        $pipe = new WriteIterator(new InputStream(), new \ArrayIterator());
    }
}
