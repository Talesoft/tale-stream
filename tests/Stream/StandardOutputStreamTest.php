<?php declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use Tale\Stream\StandardOutputStream;

/**
 * @coversDefaultClass \Tale\Stream\StandardOutputStream
 */
class StandardOutputStreamTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::isWritable
     * @covers ::isReadable
     */
    public function testConstruct(): void
    {
        $stream = new StandardOutputStream();
        self::assertTrue($stream->isWritable());
        self::assertFalse($stream->isReadable());
        $stream = null;
    }
}
