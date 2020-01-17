<?php declare(strict_types=1);

namespace Tale\Test\Stream\Iterator;

use ArrayIterator;
use CallbackFilterIterator;
use InvalidArgumentException;
use IteratorIterator;
use RuntimeException;
use Tale\Stream;
use Tale\Stream\Iterator\LineIterator;
use Tale\Stream\Iterator\ReadIterator;
use Tale\Stream\Iterator\WriteIterator;
use Tale\Stream\Exception\NotWritableException;

/**
 * @coversDefaultClass \Tale\Stream\Iterator\WriteIterator
 */
class WriteIteratorTest extends AbstractIteratorTest
{
    /**
     * @covers ::__construct
     * @covers ::getStream
     * @covers ::getSourceIterable
     * @covers ::getIterator
     * @covers ::writeAll
     */
    public function testConstruct(): void
    {
        $outputStream = Stream::createMemoryStream();
        $addLfModifier = $this->createSourceIterator();
        $writeIterator = new WriteIterator($outputStream, $addLfModifier);
        self::assertSame($outputStream, $writeIterator->getStream());
        self::assertSame($addLfModifier, $writeIterator->getSourceIterable());
        $writtenBytes = iterator_to_array($writeIterator); //The actual piping process, chunk-by-chunk
        self::assertSame([3, 3, 2], $writtenBytes);
        $outputStream->rewind();
        $writeIterator = new WriteIterator($outputStream, $this->createSourceIterator());
        self::assertSame(8, $writeIterator->writeAll());
        self::assertSame("ab\ncd\ng\n", (string)$outputStream); //"ab\ncd\ng"
    }

    private function createSourceIterator()
    {
        $inputStream = Stream::createMemoryStream("ab\ncd\nde\ng");

        // Use a LineIterator to cleanly read lines
        $reader = new LineIterator(new ReadIterator($inputStream));

        // Will filter all lines that match "de"
        $filteredReader = new CallbackFilterIterator($reader->getIterator(), fn (string $line) => $line !== 'de');

        // Will add "\n" to all lines
        return new class($filteredReader) extends IteratorIterator
        {
            public function current(): string
            {
                return parent::current()."\n";
            }
        };
    }

    /**
     * @covers ::__construct
     */
    public function testIfConstructThrowsExceptionWhenStreamIsNotWritable(): void
    {
        $this->expectException(NotWritableException::class);
        $writeIterator = new WriteIterator(Stream::createInputStream(), new ArrayIterator());
    }

    /**
     * @covers ::__construct
     * @covers ::getIterator
     * @covers ::writeAll
     * @dataProvider provideNonWritableValues
     * @param $arg
     */
    public function testIfGetIteratorThrowsExceptionOnNonWritableValue($arg): void
    {
        $this->expectException(RuntimeException::class);
        $writeIterator = new WriteIterator(Stream::createMemoryStream(), [$arg]);
        $writeIterator->writeAll();
    }

    public function provideNonWritableValues(): array
    {
        return [
            [null],
            [true],
            [1.4],
            [15],
            [[]],
            [new class {
            }],
            [stream_context_create()]
        ];
    }
}
