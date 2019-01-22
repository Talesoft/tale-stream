<?php declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use Tale\Stream\FileStream;

/**
 * @coversDefaultClass \Tale\Stream\FileStream
 */
class FileStreamTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::isReadable
     * @covers ::isWritable
     */
    public function testConstruct(): void
    {
        $stream = new FileStream('php://memory', 'rb+');
        $stream->write('test');
        self::assertSame(4, $stream->getSize());

        $stream = new FileStream('php://memory', 'rb+', false, stream_context_create());
        $stream->write('test');
        self::assertSame(4, $stream->getSize());
        $stream = null;
    }
    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructThrowsExceptionOnInvalidContextResource(): void
    {
        $stream = new FileStream('php://memory', 'rb+', false, 'test');
    }
}
