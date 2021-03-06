<?php declare(strict_types=1);

namespace Tale\Test\Stream;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;
use Tale\Stream;
use Tale\Stream\Exception\NotReadableException;
use Tale\Stream\Exception\NotSeekableException;
use Tale\Stream\Exception\NotWritableException;
use Tale\Stream\Exception\ResourceClosedException;
use Tale\Stream\Exception\ResourceInvalidException;
use function strlen;

/**
 * @coversDefaultClass \Tale\Stream
 */
class StreamTest extends TestCase
{
    public const HTTP_RESOURCE = 'https://gen.talesoft.codes?length=30';
    public const READ_RESOURCE = __DIR__.'/test-files/read-test.txt';
    public const WRITE_RESOURCE = __DIR__.'/test-files/write-test.txt';

    /**
     * @covers ::__construct
     * @covers ::__destruct
     * @covers ::close
     * @covers ::detach
     * @covers ::getMetadata
     */
    public function testConstructDestruct(): void
    {
        $fp = fopen(self::READ_RESOURCE, 'rb');
        $stream = new Stream($fp);
        self::assertIsResource($fp);
        self::assertEquals('stream', get_resource_type($fp));
        self::assertIsArray($stream->getMetadata());
        $stream = null;
        self::assertEquals('Unknown', get_resource_type($fp));

        $fp = fopen(self::READ_RESOURCE, 'rb');
        $stream = new Stream($fp);
        self::assertIsResource($fp);
        self::assertEquals('stream', get_resource_type($fp));
        self::assertIsArray($stream->getMetadata());
        $stream->close();
        self::assertEquals('Unknown', get_resource_type($fp));
        self::assertIsArray($stream->getMetadata());
        self::assertCount(0, $stream->getMetadata());

        $fp = fopen(self::READ_RESOURCE, 'rb');
        $stream = new Stream($fp);
        self::assertIsResource($fp);
        self::assertEquals('stream', get_resource_type($fp));
        self::assertIsArray($stream->getMetadata());
        $fp = $stream->detach();
        self::assertIsResource($fp);
        self::assertEquals('stream', get_resource_type($fp));
        self::assertIsArray($stream->getMetadata());
        self::assertCount(0, $stream->getMetadata());
        $stream->close();
        self::assertEquals('stream', get_resource_type($fp));
        fclose($fp);
        self::assertEquals('Unknown', get_resource_type($fp));
    }

    /**
     * @covers ::__construct
     * @dataProvider badConstructorUriProvider
     * @param $parameter
     */
    public function testConstructThrowsExceptionWithBadParameters($parameter): void
    {
        $this->expectException(InvalidArgumentException::class);
        $stream = new Stream($parameter);
    }

    public function badConstructorUriProvider(): array
    {
        return [
            [null],
            [true],
            [12],
            [23.56],
            [new class {
            }],
            [['test']],
            ['test'],
            [stream_context_create()]
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::getSize
     * @covers ::getContents
     */
    public function testGetSize(): void
    {
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        self::assertEquals(30, $stream->getSize());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->detach();
        self::assertNull($stream->getSize());

        $stream = new Stream(fopen(self::HTTP_RESOURCE, 'rb'));
        self::assertEquals(0, $stream->getSize()); // Can't get a size from HTTP Streams (the content is 30 chars long)
        self::assertEquals(30, strlen($stream->getContents())); // But we can read it!
    }

    /**
     * @covers ::__construct
     * @covers ::tell
     * @covers ::seek
     */
    public function testTell(): void
    {
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->seek(5);
        self::assertEquals(5, $stream->tell());
    }

    /**
     * @covers ::__construct
     * @covers ::tell
     */
    public function testTellThrowsExceptionOnClosedResource(): void
    {
        $this->expectException(ResourceClosedException::class);
        $resource = fopen(self::READ_RESOURCE, 'rb');
        $stream = new Stream($resource);
        fclose($resource);
        $stream->tell();
    }

    /**
     * @covers ::__construct
     * @covers ::tell
     * @throws ReflectionException
     */
    public function testTellThrowsExceptionOnInvalidResource(): void
    {
        $this->expectException(ResourceInvalidException::class);
        $resource = fopen(self::READ_RESOURCE, 'rb');
        $stream = new Stream($resource);
        $prop = new ReflectionProperty(Stream::class, 'resource');
        $prop->setAccessible(true);
        $prop->setValue($stream, stream_context_create());
        $prop->setAccessible(false);
        $stream->tell();
    }

    /**
     * @covers ::__construct
     * @covers ::eof
     * @covers ::seek
     * @covers ::read
     */
    public function testEof(): void
    {
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->detach();
        self::assertTrue($stream->eof());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        while (!$stream->eof()) {
            $stream->read(7);
        }
        self::assertEquals(30, $stream->tell());
        self::assertTrue($stream->eof());

        $stream = new Stream(fopen(__DIR__.'/test-files/read-test-eof.txt', 'rb'));
        self::assertFalse($stream->eof());
        self::assertSame('', $stream->read(1));
        self::assertTrue($stream->eof());
        $stream = null;
    }

    /**
     * @covers ::__construct
     * @covers ::isSeekable
     */
    public function testIsSeekable(): void
    {
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->detach();
        self::assertFalse($stream->isSeekable());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        self::assertTrue($stream->isSeekable()); //File streams are usually seekable

        $stream = new Stream(fopen(self::HTTP_RESOURCE, 'rb'));
        self::assertFalse($stream->isSeekable()); //HTTP streams are not seekable
    }

    /**
     * @covers ::__construct
     * @covers ::seek
     * @covers ::tell
     */
    public function testSeek(): void
    {
        //Default should be SEEK_SET
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->seek(2, Stream::SEEK_START);
        $stream->seek(8, Stream::SEEK_START);
        self::assertEquals(8, $stream->tell());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->seek(2, Stream::SEEK_START);
        $stream->seek(8, Stream::SEEK_START);
        self::assertEquals(8, $stream->tell());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->seek(-3, Stream::SEEK_END);
        $stream->seek(-8, Stream::SEEK_END);
        self::assertEquals(22, $stream->tell());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->seek(8, Stream::SEEK_CURRENT);
        $stream->seek(4, Stream::SEEK_CURRENT);
        self::assertEquals(12, $stream->tell());
    }

    /**
     * @covers ::__construct
     * @covers ::seek
     * @dataProvider provideNonIntArguments
     * @param $arg
     */
    public function testSeekThrowsExceptionOnInvalidOffset($arg): void
    {
        $this->expectException(InvalidArgumentException::class);
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->seek($arg);
    }

    /**
     * @covers ::__construct
     * @covers ::seek
     * @dataProvider provideNonIntArguments
     * @param $arg
     */
    public function testSeekThrowsExceptionOnInvalidWhence($arg): void
    {
        $this->expectException(InvalidArgumentException::class);
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->seek(0, $arg);
    }

    /**
     * @covers ::__construct
     * @covers ::seek
     */
    public function testSeekThrowsExceptionOnClosedResource(): void
    {
        $this->expectException(ResourceClosedException::class);
        $resource = fopen(self::READ_RESOURCE, 'rb');
        $stream = new Stream($resource);
        fclose($resource);
        $stream->seek(0);
    }

    /**
     * @covers ::__construct
     * @covers ::seek
     */
    public function testSeekThrowsExceptionWhenNotSeekable(): void
    {
        $this->expectException(NotSeekableException::class);
        $stream = new Stream(fopen(self::HTTP_RESOURCE, 'rb'));
        $stream->seek(5);
    }

    /**
     * @covers ::__construct
     * @covers ::rewind
     */
    public function testRewind(): void
    {
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->seek(8);
        self::assertEquals(8, $stream->tell());
        $stream->rewind();
        self::assertEquals(0, $stream->tell());
    }

    /**
     * @covers ::__construct
     * @covers ::rewind
     */
    public function testRewindThrowsExceptionWhenNotSeekable(): void
    {
        $this->expectException(NotSeekableException::class);
        $stream = new Stream(fopen(self::HTTP_RESOURCE, 'rb'));
        $stream->rewind();
    }

    /**
     * @covers ::__construct
     * @covers ::isWritable
     */
    public function testIsWritable(): void
    {
        $stream = new Stream(fopen(self::READ_RESOURCE, 'ab'));
        $stream->detach();
        self::assertFalse($stream->isWritable());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'ab'));
        self::assertTrue($stream->isWritable());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        self::assertFalse($stream->isWritable());
    }

    /**
     * @covers ::__construct
     * @covers ::write
     */
    public function testWrite(): void
    {
        $stream = new Stream(fopen(self::WRITE_RESOURCE, 'wb'));
        $stream->write('Test String');
        $stream = null;
        self::assertEquals('Test String', file_get_contents(self::WRITE_RESOURCE));

        $stream = new Stream(fopen(self::WRITE_RESOURCE, 'ab'));
        $stream->write(' with appended Text');
        $stream = null;
        self::assertEquals('Test String with appended Text', file_get_contents(self::WRITE_RESOURCE));

        unlink(self::WRITE_RESOURCE);
    }

    /**
     * @covers ::__construct
     * @covers ::write
     * @dataProvider provideNonStringArguments
     */
    public function testWriteThrowsExceptionOnInvalidContent($arg): void
    {
        $this->expectException(InvalidArgumentException::class);
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->write($arg);
    }

    /**
     * @covers ::__construct
     * @covers ::write
     */
    public function testWriteThrowsExceptionWhenNotWritable(): void
    {
        $this->expectException(NotWritableException::class);
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->write('Test String');
    }

    /**
     * @covers ::__construct
     * @covers ::write
     */
    public function testWriteThrowsExceptionOnClosedResource(): void
    {
        $this->expectException(ResourceClosedException::class);
        $resource = fopen(self::READ_RESOURCE, 'rb');
        $stream = new Stream($resource);
        fclose($resource);
        $stream->write('');
    }

    /**
     * @covers ::__construct
     * @covers ::isReadable
     */
    public function testIsReadable(): void
    {
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->detach();
        self::assertFalse($stream->isReadable());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        self::assertTrue($stream->isReadable());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'ab'));
        self::assertFalse($stream->isReadable());

        $stream = new Stream(fopen(self::WRITE_RESOURCE, 'wb'));
        self::assertFalse($stream->isReadable());
        $stream = null;
        unlink(self::WRITE_RESOURCE);
    }

    /**
     * @covers ::__construct
     * @covers ::read
     * @covers ::seek
     */
    public function testRead(): void
    {
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        self::assertEquals('This is', $stream->read(7));
        self::assertEquals("\n", $stream->read(1));
        self::assertEquals("some\n", $stream->read(5));
        $stream->seek(-5, Stream::SEEK_CURRENT);
        self::assertEquals("some\n", $stream->read(5));
        $stream = null;
    }

    /**
     * @covers ::__construct
     * @covers ::read
     * @dataProvider provideNonIntArguments
     * @param $arg
     */
    public function testReadThrowsExceptionOnInvalidLength($arg): void
    {
        $this->expectException(InvalidArgumentException::class);
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->read($arg);
    }

    /**
     * @covers ::__construct
     * @covers ::read
     */
    public function testReadThrowsExceptionOnClosedResource(): void
    {
        $this->expectException(ResourceClosedException::class);
        $resource = fopen(self::READ_RESOURCE, 'rb');
        $stream = new Stream($resource);
        fclose($resource);
        $stream->read(10);
    }

    /**
     * @covers ::__construct
     * @covers ::read
     */
    public function testReadThrowsExceptionWhenNotReadable(): void
    {
        $this->expectException(NotReadableException::class);
        $stream = new Stream(fopen(self::READ_RESOURCE, 'ab'));
        $stream->read(4);
    }

    /**
     * @covers ::__construct
     * @covers ::getContents
     * @covers ::seek
     */
    public function testGetContents(): void
    {
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        self::assertEquals("This is\nsome\nnice\ntest\nContent", $stream->getContents());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->seek(5);
        self::assertEquals("is\nsome\nnice\ntest\nContent", $stream->getContents());
        $stream = null;
    }

    /**
     * @covers ::__construct
     * @covers ::getContents
     */
    public function testGetContentsThrowsExceptionWhenNotReadable(): void
    {
        $this->expectException(RuntimeException::class);
        $stream = new Stream(fopen(self::READ_RESOURCE, 'ab'));
        $stream->getContents();
    }

    /**
     * @covers ::__construct
     * @covers ::getContents
     */
    public function testGetContentsThrowsExceptionOnClosedResource(): void
    {
        $this->expectException(ResourceClosedException::class);
        $resource = fopen(self::READ_RESOURCE, 'rb');
        $stream = new Stream($resource);
        fclose($resource);
        $stream->getContents();
    }

    /**
     * @covers ::__construct
     * @covers ::getMetadata
     */
    public function testGetMetadata(): void
    {
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        self::assertIsArray($stream->getMetadata());
        self::assertNull($stream->getMetadata('some thought up key'));
        self::assertEquals('rb', $stream->getMetadata('mode'));
    }

    /**
     * @covers ::__construct
     * @covers ::getMetadata
     * @dataProvider provideInvalidMetadataKeys
     */
    public function testGetMetadataThrowsExceptionOnInvalidKey($arg): void
    {
        $this->expectException(InvalidArgumentException::class);
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        self::assertEquals('rb', $stream->getMetadata($arg));
    }

    /**
     * @covers ::__construct
     * @covers ::__toString
     */
    public function testToString(): void
    {
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        self::assertEquals("This is\nsome\nnice\ntest\nContent", (string)$stream);
        $stream->seek(6);
        self::assertEquals("This is\nsome\nnice\ntest\nContent", (string)$stream);

        $stream = new Stream(fopen(self::READ_RESOURCE, 'ab'));
        self::assertEquals('', (string)$stream);

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->seek(5);
        self::assertEquals("is\nsome\nnice\ntest\nContent", $stream->getContents());
        $stream = null;
    }

    /**
     * @covers ::__construct
     * @covers ::__clone
     */
    public function testCloneThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $clone = clone $stream;
    }

    /**
     * @covers ::__construct
     * @covers ::createFileStream
     */
    public function testCreateFileStream(): void
    {
        $stream = Stream::createFileStream('php://memory', 'rb+');
        $stream->write('test');
        self::assertSame(4, $stream->getSize());

        $stream = Stream::createFileStream('php://memory', 'rb+', false, stream_context_create());
        $stream->write('test');
        self::assertSame(4, $stream->getSize());
        $stream = null;
    }

    /**
     * @covers ::__construct
     * @covers ::createInputStream
     */
    public function testCreateInputStream(): void
    {
        $stream = Stream::createInputStream();
        self::assertTrue($stream->isReadable());
        self::assertFalse($stream->isWritable());
        $stream = null;
    }
    /**
     * @covers ::__construct
     * @covers ::createMemoryStream
     */
    public function testCreateMemoryStream(): void
    {
        $stream = Stream::createMemoryStream();
        self::assertSame(0, $stream->getSize());
        self::assertSame(0, $stream->tell());
        self::assertSame('', $stream->getContents());

        $stream = Stream::createMemoryStream('test');
        self::assertSame(4, $stream->getSize());
        self::assertSame(0, $stream->tell());
        self::assertSame('test', $stream->getContents());
        $stream = null;
    }

    /**
     * @covers ::createMemoryStream
     * @covers ::eof
     */
    public function testMemoryStreamEof(): void
    {
        $stream = Stream::createMemoryStream();
        self::assertFalse($stream->eof());
        self::assertSame('', $stream->read(1));
        self::assertTrue($stream->eof());

        $stream = Stream::createMemoryStream('test');
        self::assertFalse($stream->eof());
    }


    /**
     * @covers ::__construct
     * @covers ::createTempStream
     */
    public function testCreateTempStream(): void
    {
        $stream = Stream::createTempStream();
        self::assertSame(0, $stream->getSize());
        self::assertSame(0, $stream->tell());
        self::assertSame('', $stream->getContents());

        $stream = Stream::createTempStream('test', 3);
        self::assertSame(4, $stream->getSize());
        self::assertSame(0, $stream->tell());
        self::assertSame('test', $stream->getContents());
    }

    /**
     * @covers ::__construct
     * @covers ::createOutputStream
     */
    public function testCreateOutputStream(): void
    {
        $stream = Stream::createOutputStream();
        self::assertTrue($stream->isWritable());
        self::assertFalse($stream->isReadable());
        $stream = null;
    }

    /**
     * @covers ::__construct
     * @covers ::createStdinStream
     */
    public function testCreateStdinStream(): void
    {
        $stream = Stream::createStdinStream();
        self::assertTrue($stream->isReadable());
        self::assertFalse($stream->isWritable());
        $stream = null;
    }

    /**
     * @covers ::__construct
     * @covers ::createStderrStream
     */
    public function testCreateStderrStream(): void
    {
        $stream = Stream::createStderrStream();
        self::assertTrue($stream->isWritable());
        self::assertFalse($stream->isReadable());
        $stream = null;
    }
    /**
     * @covers ::__construct
     * @covers ::createStdoutStream
     */
    public function testCreateStdoutStream(): void
    {
        $stream = Stream::createStdoutStream();
        self::assertTrue($stream->isWritable());
        self::assertFalse($stream->isReadable());
        $stream = null;
    }

    public function provideNonStringArguments(): array
    {
        return [
            [null],
            [true],
            [1.4],
            [15],
            [[]],
            [new class {
            }],
            [stream_context_create()]
        ];
    }

    public function provideNonIntArguments(): array
    {
        return [
            [null],
            [true],
            [1.4],
            ['test'],
            [[]],
            [new class {
            }],
            [stream_context_create()]
        ];
    }

    public function provideInvalidMetadataKeys(): array
    {
        return [
            [true],
            [1.5],
            [1.4],
            [[]],
            [new class {
            }],
            [stream_context_create()]
        ];
    }
}
