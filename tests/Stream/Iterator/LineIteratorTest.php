<?php declare(strict_types=1);

namespace Tale\Test\Stream\Iterator;

use Tale\Stream;
use Tale\Stream\Iterator\LineIterator;
use Tale\Stream\Iterator\ReadIterator;

/**
 * @coversDefaultClass \Tale\Stream\Iterator\LineIterator
 */
class LineIteratorTest extends AbstractIteratorTest
{
    /**
     * @covers ::__construct
     * @covers ::getIterator
     * @covers ::getReadIterator
     * @covers ::getDelimiter
     */
    public function testConstruct(): void
    {
        for ($i = 1; $i <= 51; $i += 5) {
            $stream = Stream::createTempStream("line 1\nline 2\nline 3");
            $readIterator = new ReadIterator($stream, $i);
            $iterator = new LineIterator($readIterator, LineIterator::DELIMITER_LF);
            self::assertSame(LineIterator::DELIMITER_LF, $iterator->getDelimiter());
            self::assertSame($readIterator, $iterator->getReadIterator());
            self::assertIterator(['line 1', 'line 2', 'line 3'], $iterator);

            $stream = Stream::createTempStream("line 1\nline 2\nline 3\n");
            $iterator = new LineIterator(new ReadIterator($stream, $i), LineIterator::DELIMITER_LF);
            self::assertIterator(['line 1', 'line 2', 'line 3', ''], $iterator);

            $stream = Stream::createTempStream("\nline 1\nline 2\nline 3");
            $iterator = new LineIterator(new ReadIterator($stream, $i), LineIterator::DELIMITER_LF);
            self::assertIterator(['', 'line 1', 'line 2', 'line 3'], $iterator);

            $stream = Stream::createTempStream("\nline 1\nline 2\nline 3\n");
            $iterator = new LineIterator(new ReadIterator($stream, $i), LineIterator::DELIMITER_LF);
            self::assertIterator(['', 'line 1', 'line 2', 'line 3', ''], $iterator);

            $stream = Stream::createTempStream("\r\nline 1\r\nline 2\r\nline 3\r\n");
            $iterator = new LineIterator(new ReadIterator($stream, $i), LineIterator::DELIMITER_LF);
            self::assertIterator(['', 'line 1', 'line 2', 'line 3', ''], $iterator);
        }
    }
}
