<?php
declare(strict_types=1);

namespace Tale\Test\Stream\Iterator;

use PHPUnit\Framework\TestCase;
use Tale\Stream\Iterator\ReadIterator;
use Tale\Stream\OutputStream;
use Tale\Stream\TempStream;

/**
 * @coversDefaultClass \Tale\Stream\Iterator\ReadIterator
 */
class ReadIteratorTest extends TestCase
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
        $loops = 0;
        $this->assertFalse($iterator->eof());
        foreach ($iterator as $chunk) {
            $this->assertEquals('test', $chunk);
            $this->assertTrue($iterator->eof());
            $loops++;
        }
        $this->assertEquals(1, $loops);

        $iterator->rewind();

        $this->assertSame($iterator, $iterator->setChunkSize(3));
        $this->assertEquals(3, $iterator->getChunkSize());

        $loops = 0;
        foreach ($iterator as $chunk) {
            switch ($loops) {
                case 0:
                    $this->assertEquals('tes', $chunk);
                    $this->assertFalse($iterator->eof());
                    break;
                case 1:
                    $this->assertEquals('t', $chunk);
                    $this->assertTrue($iterator->eof());
                    break;
            }
            $loops++;
        }
        $this->assertEquals(2, $loops);
        $stream = null;
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
