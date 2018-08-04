<?php
declare(strict_types=1);

namespace Tale\Test\Stream\Iterator;

use PHPUnit\Framework\TestCase;
use Tale\Stream\Iterator\LineIterator;
use Tale\Stream\Iterator\ReadIterator;
use Tale\Stream\Iterator\SplitIterator;
use Tale\Stream\OutputStream;
use Tale\Stream\TempStream;

/**
 * @coversDefaultClass \Tale\Stream\Iterator\LineIterator
 */
class LineIteratorTest extends AbstractIteratorTest
{
    /**
     * @covers ::__construct
     * @covers ::getIterator
     */
    public function testConstruct(): void
    {
        for ($i = 1; $i <= 51; $i += 5) {
            $stream = new TempStream("line 1\nline 2\nline 3");
            $readIterator = new ReadIterator($stream, $i);
            $iterator = new LineIterator($readIterator);

            $this->assertIterator($iterator, ['line 1', 'line 2', 'line 3']);

            $stream = new TempStream("line 1\nline 2\nline 3\n");
            $readIterator = new ReadIterator($stream, $i);
            $iterator = new LineIterator($readIterator);

            $this->assertIterator($iterator, ['line 1', 'line 2', 'line 3', '']);

            $stream = new TempStream("\nline 1\nline 2\nline 3");
            $readIterator = new ReadIterator($stream, $i);
            $iterator = new LineIterator($readIterator);

            $this->assertIterator($iterator, ['', 'line 1', 'line 2', 'line 3']);

            $stream = new TempStream("\nline 1\nline 2\nline 3\n");
            $readIterator = new ReadIterator($stream, $i);
            $iterator = new LineIterator($readIterator);

            $this->assertIterator($iterator, ['', 'line 1', 'line 2', 'line 3', '']);

            $stream = new TempStream("\r\nline 1\r\nline 2\r\nline 3\r\n");
            $readIterator = new ReadIterator($stream, $i);
            $iterator = new LineIterator($readIterator);

            $this->assertIterator($iterator, ['', 'line 1', 'line 2', 'line 3', '']);
        }
    }
}