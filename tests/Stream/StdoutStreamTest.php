<?php
declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use Tale\Stream\StdoutStream;

/**
 * @coversDefaultClass \Tale\Stream\StdoutStream
 */
class StdoutStreamTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::isWritable
     * @covers ::isReadable
     */
    public function testConstruct()
    {
        $stream = new StdoutStream();
        $this->assertTrue($stream->isWritable());
        $this->assertFalse($stream->isReadable());
    }
}
