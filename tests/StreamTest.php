<?php
declare(strict_types=1);

namespace Tale\Test\Stream;

use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tale\Stream;

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
        $this->assertInternalType('resource', $fp);
        $this->assertEquals('stream', get_resource_type($fp));
        $this->assertInternalType('array', $stream->getMetadata());
        $stream = null;
        $this->assertEquals('Unknown', get_resource_type($fp));

        $fp = fopen(self::READ_RESOURCE, 'rb');
        $stream = new Stream($fp);
        $this->assertInternalType('resource', $fp);
        $this->assertEquals('stream', get_resource_type($fp));
        $this->assertInternalType('array', $stream->getMetadata());
        $stream->close();
        $this->assertEquals('Unknown', get_resource_type($fp));
        $this->assertNull($stream->getMetadata());

        $fp = fopen(self::READ_RESOURCE, 'rb');
        $stream = new Stream($fp);
        $this->assertInternalType('resource', $fp);
        $this->assertEquals('stream', get_resource_type($fp));
        $this->assertInternalType('array', $stream->getMetadata());
        $fp = $stream->detach();
        $this->assertInternalType('resource', $fp);
        $this->assertEquals('stream', get_resource_type($fp));
        $this->assertNull($stream->getMetadata());
        $stream->close();
        $this->assertEquals('stream', get_resource_type($fp));
        fclose($fp);
        $this->assertEquals('Unknown', get_resource_type($fp));
    }

    /**
     * @covers ::__construct
     * @dataProvider badConstructorUriProvider
     */
    public function testConstructThrowsExceptionWithBadParameters($parameter): void
    {
        $this->expectException(\InvalidArgumentException::class);
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
            ['test']
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
        $this->assertEquals(30, $stream->getSize());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->detach();
        $this->assertNull($stream->getSize());

        $stream = new Stream(fopen(self::HTTP_RESOURCE, 'rb'));
        $this->assertEquals(0, $stream->getSize()); //Can't get a size from HTTP Streams (the content is 30 chars long)
        $this->assertEquals(30, \strlen($stream->getContents())); //But we can read it!
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
        $this->assertEquals(5, $stream->tell());
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
        $this->assertTrue($stream->eof());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        while (!$stream->eof()) {
            $stream->read(7);
        }
        $this->assertEquals(30, $stream->tell());
        $this->assertTrue($stream->eof());
    }

    /**
     * @covers ::__construct
     * @covers ::isSeekable
     */
    public function testIsSeekable(): void
    {
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->detach();
        $this->assertFalse($stream->isSeekable());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $this->assertTrue($stream->isSeekable()); //File streams are usually seekable

        $stream = new Stream(fopen(self::HTTP_RESOURCE, 'rb'));
        $this->assertFalse($stream->isSeekable()); //HTTP streams are not seekable
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
        $this->assertTrue($stream->seek(2));
        $this->assertTrue($stream->seek(8));
        $this->assertEquals(8, $stream->tell());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $this->assertTrue($stream->seek(2, SEEK_SET));
        $this->assertTrue($stream->seek(8, SEEK_SET));
        $this->assertEquals(8, $stream->tell());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $this->assertTrue($stream->seek(-3, SEEK_END));
        $this->assertTrue($stream->seek(-8, SEEK_END));
        $this->assertEquals(22, $stream->tell());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $this->assertTrue($stream->seek(8, SEEK_CUR));
        $this->assertTrue($stream->seek(4, SEEK_CUR));
        $this->assertEquals(12, $stream->tell());
    }

    /**
     * @covers ::__construct
     * @covers ::seek
     */
    public function testSeekThrowsExceptionWhenNotSeekable(): void
    {
        $this->expectException(Stream\Exception\NotSeekableException::class);

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
        $this->assertTrue($stream->seek(8));
        $this->assertEquals(8, $stream->tell());
        $this->assertTrue($stream->rewind());
        $this->assertEquals(0, $stream->tell());
    }

    /**
     * @covers ::__construct
     * @covers ::rewind
     */
    public function testRewindThrowsExceptionWhenNotSeekable(): void
    {
        $this->expectException(Stream\Exception\NotSeekableException::class);

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
        $this->assertFalse($stream->isWritable());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'ab'));
        $this->assertTrue($stream->isWritable());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $this->assertFalse($stream->isWritable());
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
        $this->assertEquals('Test String', file_get_contents(self::WRITE_RESOURCE));

        $stream = new Stream(fopen(self::WRITE_RESOURCE, 'ab'));
        $stream->write(' with appended Text');
        $stream = null;
        $this->assertEquals('Test String with appended Text', file_get_contents(self::WRITE_RESOURCE));

        unlink(self::WRITE_RESOURCE);
    }

    /**
     * @covers ::__construct
     * @covers ::write
     */
    public function testWriteThrowsExceptionWhenNotWritable(): void
    {
        $this->expectException(Stream\Exception\NotWritableException::class);

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->write('Test String');
    }

    /**
     * @covers ::__construct
     * @covers ::isReadable
     */
    public function testIsReadable(): void
    {
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->detach();
        $this->assertFalse($stream->isReadable());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $this->assertTrue($stream->isReadable());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'ab'));
        $this->assertFalse($stream->isReadable());

        $stream = new Stream(fopen(self::WRITE_RESOURCE, 'wb'));
        $this->assertFalse($stream->isReadable());
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
        $this->assertEquals('This is', $stream->read(7));
        $this->assertEquals("\n", $stream->read(1));
        $this->assertEquals("some\n", $stream->read(5));
        $this->assertTrue($stream->seek(-5, SEEK_CUR));
        $this->assertEquals("some\n", $stream->read(5));
        $stream = null;
    }

    /**
     * @covers ::__construct
     * @covers ::read
     */
    public function testReadThrowsExceptionWhenNotReadable(): void
    {
        $this->expectException(Stream\Exception\NotReadableException::class);

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
        $this->assertEquals("This is\nsome\nnice\ntest\nContent", $stream->getContents());

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->seek(5);
        $this->assertEquals("is\nsome\nnice\ntest\nContent", $stream->getContents());
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
     * @covers ::getMetadata
     */
    public function testGetMetadata(): void
    {
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $this->assertInternalType('array', $stream->getMetadata());
        $this->assertNull($stream->getMetadata('some thought up key'));
        $this->assertEquals('rb', $stream->getMetadata('mode'));
    }

    /**
     * @covers ::__construct
     * @covers ::__toString
     */
    public function testToString(): void
    {
        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $this->assertEquals("This is\nsome\nnice\ntest\nContent", (string)$stream);
        $stream->seek(6);
        $this->assertEquals("This is\nsome\nnice\ntest\nContent", (string)$stream);

        $stream = new Stream(fopen(self::READ_RESOURCE, 'ab'));
        $this->assertEquals('', (string)$stream);

        $stream = new Stream(fopen(self::READ_RESOURCE, 'rb'));
        $stream->seek(5);
        $this->assertEquals("is\nsome\nnice\ntest\nContent", $stream->getContents());
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
}
