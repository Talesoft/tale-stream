<?php
declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use Tale\Stream\StdinStream;

/**
 * @coversDefaultClass \Tale\Stream\StdinStream
 */
class StdinStreamTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::isReadable
     * @covers ::isWritable
     */
    public function testConstruct(): void
    {
        $stream = new StdinStream();
        $this->assertTrue($stream->isReadable());
        $this->assertFalse($stream->isWritable());
        $stream = null;
    }
}
