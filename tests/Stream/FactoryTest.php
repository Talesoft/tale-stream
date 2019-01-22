<?php declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use Tale\Stream\Factory;

/**
 * @coversDefaultClass \Tale\Stream\Factory
 */
class FactoryTest extends TestCase
{
    /**
     * @covers ::createStream
     * @covers ::createStreamFromFile
     * @covers ::createStreamFromResource
     */
    public function testConstruct(): void
    {
        $factory = new Factory();
        $stream = $factory->createStream('test');
        self::assertSame(4, $stream->getSize());

        $stream = $factory->createStreamFromFile('php://memory', 'rb+');
        $stream->write('test');
        self::assertSame(4, $stream->getSize());

        $stream = $factory->createStreamFromResource(fopen('php://memory', 'rb+'));
        $stream->write('test');
        self::assertSame(4, $stream->getSize());
        $stream = null;
    }
}
