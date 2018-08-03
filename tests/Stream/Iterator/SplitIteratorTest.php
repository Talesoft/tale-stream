<?php
declare(strict_types=1);

namespace Tale\Test\Stream\Iterator;

use PHPUnit\Framework\TestCase;
use Tale\Stream\Iterator\ReadIterator;
use Tale\Stream\Iterator\SplitIterator;
use Tale\Stream\OutputStream;
use Tale\Stream\TempStream;

/**
 * @coversDefaultClass \Tale\Stream\Iterator\SplitIterator
 */
class SplitIteratorTest extends AbstractIteratorTest
{
    /**
     * @covers ::__construct
     * @covers ::getReadIterator
     * @covers ::getDelimiter
     * @covers ::setDelimiter
     * @covers ::getIterator
     */
    public function testConstruct(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $stream = new TempStream('a,b,c,d');
            $readIterator = new ReadIterator($stream, $i);
            $iterator = new SplitIterator($readIterator, '|');

            $this->assertEquals($readIterator, $iterator->getReadIterator());
            $this->assertEquals('|', $iterator->getDelimiter());
            $this->assertSame($iterator, $iterator->setDelimiter(','));
            $this->assertEquals(',', $iterator->getDelimiter());

            $this->assertIterator($iterator, ['a', 'b', 'c', 'd']);
        }
    }
}
