<?php declare(strict_types=1);

namespace Tale;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Tale\Stream\Exception\NotReadableException;
use Tale\Stream\Exception\NotSeekableException;
use Tale\Stream\Exception\NotWritableException;
use Tale\Stream\Exception\ResourceClosedException;
use Tale\Stream\Exception\ResourceInvalidException;

/**
 * A basic PSR-7 Stream implementation for common usage (not restricted to HTTP streams).
 *
 * Should act as a base class for more sophisticated stream classes.
 *
 * @package Tale
 */
final class Stream implements StreamInterface
{
    /**
     * Tells ->seek() to move from the start of the stream.
     */
    public const SEEK_START = \SEEK_SET;

    /**
     * Tells ->seek() to move from the current stream position.
     */
    public const SEEK_CURRENT = \SEEK_CUR;

    /**
     * Tells ->seek() to move from the end of the stream.
     */
    public const SEEK_END = \SEEK_END;

    public const PHP_INPUT = 'php://input';
    public const PHP_OUTPUT = 'php://output';
    public const PHP_MEMORY = 'php://memory';
    public const PHP_TEMP = 'php://temp';
    public const PHP_STDIN = 'php://stdin';
    public const PHP_STDERR = 'php://stderr';
    public const PHP_STDOUT = 'php://stdout';

    /**
     * The current stream context resource.
     *
     * @var resource
     */
    private $resource;

    /**
     * An array of meta data information.
     *
     * @var array
     */
    private $metadata;


    /**
     * Creates a new stream instance based on a passed resource.
     *
     * @param resource $resource A PHP resource of type 'stream'.
     *
     * @throws InvalidArgumentException
     */
    public function __construct($resource)
    {
        if (!\is_resource($resource)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 1 passed to %s->__construct needs to be resource, %s given',
                static::class,
                \gettype($resource)
            ));
        }

        if (($streamType = get_resource_type($resource)) !== 'stream') {
            throw new InvalidArgumentException(sprintf(
                'Argument 1 passed to %s->__construct needs to be resource of type stream, type %s given',
                static::class,
                $streamType
            ));
        }

        $this->resource = $resource;
        $this->metadata = stream_get_meta_data($this->resource);
    }

    /**
     * Closes the stream upon deconstruction of this object
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * {@inheritdoc}
     */
    public function close(): void
    {
        if (!\is_resource($this->resource)) {
            return;
        }

        $context = $this->detach();
        @fclose($context);
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;
        $this->metadata = null;
        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize(): ?int
    {
        if (!\is_resource($this->resource)) {
            return null;
        }

        $stat = fstat($this->resource);
        return (int)$stat['size'];
    }

    /**
     * {@inheritdoc}
     */
    public function tell(): int
    {
        if (!\is_resource($this->resource)) {
            throw new ResourceClosedException('Failed to tell stream position: Resource is closed.');
        }
        $offset = @ftell($this->resource);
        if ($offset === false) {
            throw new ResourceInvalidException('Failed to tell stream position: Resource is invalid.');
        }
        return $offset;
    }

    /**
     * {@inheritdoc}
     */
    public function eof(): bool
    {
        if (!\is_resource($this->resource)) {
            return true;
        }
        return feof($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable(): bool
    {
        if (!\is_resource($this->resource)) {
            return false;
        }

        return $this->getMetadata('seekable') ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = self::SEEK_START): void
    {
        if (!\is_int($offset)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 1 passed to %s->seek needs to be int, %s given',
                static::class,
                gettype($offset)
            ));
        }

        if (!\is_int($whence)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 2 passed to %s->seek needs to be int, %s given',
                static::class,
                gettype($whence)
            ));
        }

        if (!\is_resource($this->resource)) {
            throw new ResourceClosedException('Failed to seek: Resource is closed.');
        }

        if (!$this->isSeekable()) {
            throw new NotSeekableException('Failed to seek: Stream is not seekable');
        }

        // @codeCoverageIgnoreStart
        //I don't know how to fabricate this error manually, so I can't test it.
        if (fseek($this->resource, $offset, $whence) === -1) {
            throw new ResourceInvalidException('Failed to seek: Resource is invalid.');
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable(): bool
    {
        if (!\is_resource($this->resource)) {
            return false;
        }

        $mode = $this->getMetadata('mode');
        return (strpos($mode, 'w') !== false || strpos($mode, 'x') !== false
            || strpos($mode, 'c') !== false  || strpos($mode, '+') !== false
            || strpos($mode, 'a') !== false);
    }

    /**
     * {@inheritdoc}
     */
    public function write($string): int
    {
        if (!\is_string($string)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 1 passed to %s->write needs to be string, %s given',
                static::class,
                gettype($string)
            ));
        }

        if (!\is_resource($this->resource)) {
            throw new ResourceClosedException('Failed to write: Resource is closed.');
        }

        if (!$this->isWritable()) {
            throw new NotWritableException('Failed to write: Stream is not writable');
        }

        // @codeCoverageIgnoreStart
        //I don't know how to fabricate this error manually, so I can't test it.
        if (($writtenBytes = fwrite($this->resource, $string)) === false) {
            throw new ResourceInvalidException('Failed to write: Resource is invalid.');
        }
        // @codeCoverageIgnoreEnd
        return $writtenBytes;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable(): bool
    {
        if (!\is_resource($this->resource)) {
            return false;
        }

        $mode = $this->getMetadata('mode');
        return (strpos($mode, 'r') !== false  || strpos($mode, '+') !== false );
    }

    /**
     * {@inheritdoc}
     */
    public function read($length): string
    {
        if (!\is_int($length)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 1 passed to %s->read needs to be int, %s given',
                static::class,
                gettype($length)
            ));
        }

        if (!\is_resource($this->resource)) {
            throw new ResourceClosedException('Failed to read: Resource is closed.');
        }

        if (!$this->isReadable()) {
            throw new NotReadableException('Failed to read: Stream is not readable');
        }

        // @codeCoverageIgnoreStart
        //I don't know how to fabricate this error manually, so I can't test it.
        if (($content = fread($this->resource, $length)) === false) {
            throw new ResourceInvalidException('Failed to read: Resource is invalid.');
        }
        // @codeCoverageIgnoreEnd
        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents(): string
    {
        if (!\is_resource($this->resource)) {
            throw new ResourceClosedException('Failed to get contents: Resource is closed.');
        }

        if (!$this->isReadable()) {
            throw new NotReadableException('Failed to get contents: Stream is not readable');
        }

        return stream_get_contents($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        if (!\is_string($key) && $key !== null) {
            throw new InvalidArgumentException(sprintf(
                'Argument 1 passed to %s->getMetadata needs to be string or null, %s given',
                static::class,
                gettype($key)
            ));
        }

        if ($key === null) {
            return $this->metadata;
        }

        if (!isset($this->metadata[$key])) {
            return null;
        }

        return $this->metadata[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        try {
            if ($this->isReadable() && $this->isSeekable()) {
                $this->rewind();
            }
            return $this->getContents();
        } catch (\Throwable $ex) {
            return '';
        }
    }

    /**
     * Disallow cloning of streams
     *
     * @throws \RuntimeException
     */
    public function __clone()
    {
        $this->resource = null;
        $this->metadata = null;
        throw new \RuntimeException('Streams cannot be cloned');
    }

    public static function createFileStream(
        string $filename,
        string $mode = 'rb',
        bool $useIncludePath = false,
        $context = null
    ): self {
    
        return new self($context === null
            ? fopen($filename, $mode, $useIncludePath)
            : fopen($filename, $mode, $useIncludePath, $context)
        );
    }

    public static function createInputStream(): self
    {
        return self::createFileStream(self::PHP_INPUT);
    }

    public static function createOutputStream(): self
    {
        return self::createFileStream(self::PHP_OUTPUT, 'wb');
    }

    public static function createMemoryStream(string $content = ''): self
    {
        $stream = self::createFileStream(self::PHP_MEMORY, 'rb+');
        if ($content) {
            $stream->write($content);
            $stream->rewind();
        }
        return $stream;
    }

    public static function createTempStream(string $content = '', ?int $maxMemory = null): self
    {
        $handle = self::PHP_TEMP;
        if ($maxMemory === null) {
            $handle .= "/maxmemory:{$maxMemory}";
        }
        $stream = self::createFileStream($handle, 'rb+');
        if ($content) {
            $stream->write($content);
            $stream->rewind();
        }
        return $stream;
    }

    public static function createStdinStream(): self
    {
        return self::createFileStream(self::PHP_STDIN);
    }

    public static function createStderrStream(): self
    {
        return self::createFileStream(self::PHP_STDERR, 'wb');
    }

    public static function createStdoutStream(): self
    {
        return self::createFileStream(self::PHP_STDOUT, 'wb');
    }
}
