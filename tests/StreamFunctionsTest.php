<?php declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Tale\Stream;
use function Tale\stream;
use function Tale\stream_factory;
use function Tale\stream_file;
use function Tale\stream_get_lines;
use function Tale\stream_input;
use function Tale\stream_read_lines;
use function Tale\stream_read;
use function Tale\stream_read_split;
use function Tale\stream_write;
use function Tale\stream_write_all;
use function Tale\stream_memory;
use function Tale\stream_null;
use function Tale\stream_output;
use function Tale\stream_pipe;
use function Tale\stream_pipe_all;
use function Tale\stream_split;
use function Tale\stream_stderr;
use function Tale\stream_stdin;
use function Tale\stream_stdout;
use function Tale\stream_temp;
use Tale\StreamFactory;

class StreamFunctionsTest extends TestCase
{
    /**
     * @covers ::\Tale\stream
     */
    public function testStream(): void
    {
        $stream = stream(fopen('php://memory', 'rb+'));
        self::assertInstanceOf(Stream::class, $stream);
        $stream->close();
    }

    /**
     * @covers ::\Tale\stream_factory
     */
    public function testStreamFactory(): void
    {
        $factory = stream_factory();
        self::assertInstanceOf(StreamFactory::class, $factory);
    }

    /**
     * @covers ::\Tale\stream_file
     */
    public function testStreamFile(): void
    {
        $stream = stream_file('php://memory', 'rb+');
        self::assertInstanceOf(Stream::class, $stream);
        $stream->close();
    }

    /**
     * @covers ::\Tale\stream_input
     */
    public function testStreamInput(): void
    {
        $stream = stream_input();
        self::assertInstanceOf(Stream::class, $stream);
        $stream->close();
    }

    /**
     * @covers ::\Tale\stream_output
     */
    public function testStreamOutput(): void
    {
        $stream = stream_output();
        self::assertInstanceOf(Stream::class, $stream);
        $stream->close();
    }

    /**
     * @covers ::\Tale\stream_memory
     */
    public function testStreamMemory(): void
    {
        $stream = stream_memory('Test Content');
        self::assertInstanceOf(Stream::class, $stream);
        self::assertSame('Test Content', (string)$stream);
        $stream->close();
    }

    /**
     * @covers ::\Tale\stream_temp
     */
    public function testStreamTemp(): void
    {
        $stream = stream_temp('Test Content');
        self::assertInstanceOf(Stream::class, $stream);
        self::assertSame('Test Content', (string)$stream);
        $stream->close();
    }

    /**
     * @covers ::\Tale\stream_stdin
     */
    public function testStreamStdin(): void
    {
        $stream = stream_stdin();
        self::assertInstanceOf(Stream::class, $stream);
        $stream->close();
    }

    /**
     * @covers ::\Tale\stream_stderr
     */
    public function testStreamStderr(): void
    {
        $stream = stream_stderr();
        self::assertInstanceOf(Stream::class, $stream);
        $stream->close();
    }

    /**
     * @covers ::\Tale\stream_stdout
     */
    public function testStreamStdout(): void
    {
        $stream = stream_stdout();
        self::assertInstanceOf(Stream::class, $stream);
        $stream->close();
    }

    /**
     * @covers ::\Tale\stream_null
     */
    public function testStreamNull(): void
    {
        $stream = stream_null();
        self::assertSame('', $stream->read(1024));
        $stream->close();
    }

    /**
     * @covers ::\Tale\stream_read_lines
     */
    public function testStreamReadLines(): void
    {
        $reader = stream_read(stream_memory("Line 1\nLine 2\nLine 3"));
        $iter = stream_read_lines($reader);
        self::assertSame(['Line 1', 'Line 2', 'Line 3'], iterator_to_array($iter));
    }

    /**
     * @covers ::\Tale\stream_get_lines
     */
    public function testStreamGetLines(): void
    {
        $lines = stream_get_lines(stream_memory("Line 1\nLine 2\nLine 3"));
        self::assertSame(['Line 1', 'Line 2', 'Line 3'], iterator_to_array($lines));
    }

    /**
     * @covers ::\Tale\stream_read
     */
    public function testStreamRead(): void
    {
        $reader = stream_read(stream_memory("Line 1\nLine 2\nLine 3"), 10);
        self::assertSame(["Line 1\nLin", "e 2\nLine 3"], iterator_to_array($reader));
    }

    /**
     * @covers ::\Tale\stream_read_split
     */
    public function testStreamReadSplit(): void
    {
        $reader = stream_read(stream_memory('a,b,c,d'));
        $iter = stream_read_split($reader, ',');
        self::assertSame(['a', 'b', 'c', 'd'], iterator_to_array($iter));
    }

    /**
     * @covers ::\Tale\stream_split
     */
    public function testStreamSplit(): void
    {
        $items = stream_split(stream_memory('a,b,c,d'), ',');
        self::assertSame(['a', 'b', 'c', 'd'], iterator_to_array($items));
    }

    /**
     * @covers ::\Tale\stream_write
     */
    public function testStreamWrite(): void
    {
        $generateLines = static function () {
            yield "Line 1\n";
            yield "Line 2\n";
            yield "Line 3\n";
        };
        $stream = stream_memory();
        $writer = stream_write($stream, $generateLines());
        self::assertSame(21, $writer->writeAll());
        $stream->rewind();
        self::assertSame("Line 1\nLine 2\nLine 3\n", $stream->getContents());
        $stream->close();
    }

    /**
     * @covers ::\Tale\stream_write_all
     */
    public function testStreamWriteAll(): void
    {
        $generateLines = static function () {
            yield "Line 1\n";
            yield "Line 2\n";
            yield "Line 3\n";
        };
        $stream = stream_memory();
        self::assertSame(21, stream_write_all($stream, $generateLines()));
        $stream->rewind();
        self::assertSame("Line 1\nLine 2\nLine 3\n", $stream->getContents());
        $stream->close();
    }

    /**
     * @covers ::\Tale\stream_pipe
     */
    public function testStreamPipe(): void
    {
        $generateLines = static function () {
            yield "Line 1\n";
            yield "Line 2\n";
            yield "Line 3\n";
        };
        $stream = stream_memory();
        self::assertSame(21, stream_write_all($stream, $generateLines()));
        $stream->rewind();
        $targetStream = stream_memory();
        $writer = stream_pipe($stream, $targetStream);
        self::assertSame(21, $writer->writeAll());
        $targetStream->rewind();
        self::assertSame("Line 1\nLine 2\nLine 3\n", $targetStream->getContents());
        $stream->close();
        $targetStream->close();
    }

    /**
     * @covers ::\Tale\stream_pipe_all
     */
    public function testStreamPipeAll(): void
    {
        $generateLines = static function () {
            yield "Line 1\n";
            yield "Line 2\n";
            yield "Line 3\n";
        };
        $stream = stream_memory();
        self::assertSame(21, stream_write_all($stream, $generateLines()));
        $stream->rewind();
        $targetStream = stream_memory();
        self::assertSame(21, stream_pipe_all($stream, $targetStream));
        $targetStream->rewind();
        self::assertSame("Line 1\nLine 2\nLine 3\n", $targetStream->getContents());
        $stream->close();
        $targetStream->close();
    }
}
