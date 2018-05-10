<?php
declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use Tale\Stream\TempStream;

/**
 * @coversDefaultClass \Tale\Stream\TempStream
 */
class TempStreamTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::isWritable
     * @covers ::isReadable
     */
    public function testConstruct()
    {
        $stream = new TempStream('wb');
        $this->assertTrue($stream->isWritable());
        $this->assertTrue($stream->isReadable());

        $stream = new TempStream('rb');
        $this->assertTrue($stream->isReadable());
        $this->assertFalse($stream->isWritable());

        $stream = new TempStream('rb+');
        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isWritable());

        $stream = new TempStream('rb+', 4096);
        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isWritable());
    }
}
