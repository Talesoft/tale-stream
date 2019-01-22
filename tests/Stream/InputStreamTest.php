<?php declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use Tale\Stream\InputStream;

/**
 * @coversDefaultClass \Tale\Stream\InputStream
 */
class InputStreamTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::isReadable
     * @covers ::isWritable
     */
    public function testConstruct(): void
    {
        $stream = new InputStream();
        self::assertTrue($stream->isReadable());
        self::assertFalse($stream->isWritable());
        $stream = null;
    }
}
