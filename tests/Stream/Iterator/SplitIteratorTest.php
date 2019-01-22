<?php declare(strict_types=1);

namespace Tale\Test\Stream\Iterator;

use Tale\Stream\Iterator\ReadIterator;
use Tale\Stream\Iterator\SplitIterator;
use Tale\Stream\TempStream;

/**
 * @coversDefaultClass \Tale\Stream\Iterator\SplitIterator
 */
class SplitIteratorTest extends AbstractIteratorTest
{
    /**
     * @covers ::__construct
     * @covers ::getDelimiter
     * @covers ::getIterator
     * @covers ::getReadIterator
     */
    public function testConstruct(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $stream = new TempStream('a,b,c,d');
            $readIterator = new ReadIterator($stream, $i);
            $iterator = new SplitIterator($readIterator, ',');
            self::assertSame(',', $iterator->getDelimiter());
            self::assertSame($readIterator, $iterator->getReadIterator());
            self::assertIterator(['a', 'b', 'c', 'd'], $iterator);
        }
    }
}
