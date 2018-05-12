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
    public function testConstruct()
    {
        $resource = fopen(__DIR__.'/../test-files/read-test.txt', 'rb');
        $stream = new StdinStream($resource);
        $this->assertTrue($stream->isReadable());
        $this->assertFalse($stream->isWritable());
    }
}
