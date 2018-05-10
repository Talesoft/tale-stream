<?php
declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use Tale\Stream\StderrStream;

/**
 * @coversDefaultClass \Tale\Stream\StderrStream
 */
class StderrStreamTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::isWritable
     * @covers ::isReadable
     */
    public function testConstruct()
    {
        $stream = new StderrStream();
        $this->assertTrue($stream->isWritable());
        $this->assertFalse($stream->isReadable());
    }
}
