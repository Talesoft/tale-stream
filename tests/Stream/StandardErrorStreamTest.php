<?php declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use Tale\Stream\StandardErrorStream;

/**
 * @coversDefaultClass \Tale\Stream\StandardErrorStream
 */
class StandardErrorStreamTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::isWritable
     * @covers ::isReadable
     */
    public function testConstruct(): void
    {
        $stream = new StandardErrorStream();
        self::assertTrue($stream->isWritable());
        self::assertFalse($stream->isReadable());
        $stream = null;
    }
}
