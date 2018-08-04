<?php
declare(strict_types=1);

namespace Tale;

use Psr\Http\Message\StreamInterface;
use Tale\Stream\Exception\NotReadableException;
use Tale\Stream\Exception\NotSeekableException;
use Tale\Stream\Exception\NotWritableException;
use Tale\Stream\Exception\ResourceClosedException;
use Tale\Stream\Exception\ResourceInvalidException;

/**
 * Class Stream
 *
 * @package Tale
 */
class Stream implements StreamInterface
{
    public const SEEK_START = \SEEK_SET;
    public const SEEK_CURRENT = \SEEK_CUR;
    public const SEEK_END = \SEEK_END;

    /**
     * The current stream context (file resource)
     *
     * @var resource
     */
    private $resource;

    /**
     * An array of meta data information
     *
     * @var array
     */
    private $metadata;


    /**
     * Stream constructor.
     *
     * @param resource $resource
     * @throws \InvalidArgumentException
     */
    public function __construct($resource)
    {
        if (!\is_resource($resource)) {
            throw new \InvalidArgumentException(sprintf(
                'Argument 1 passed to %s->__construct needs to be resource, %s given',
                static::class,
                \gettype($resource)
            ));
        }

        if (($streamType = get_resource_type($resource)) !== 'stream') {
            throw new \InvalidArgumentException(sprintf(
                'Argument 1 passed to %s->__construct needs to be resource of type stream, type %s given',
                static::class,
                $streamType
            ));
        }

        $this->resource = $resource;
        $this->metadata = stream_get_meta_data($this->resource);
    }

    /**
     *
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
        fclose($context);
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        $context = $this->resource;
        $this->resource = null;
        $this->metadata = null;

        return $context;
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
            throw new ResourceClosedException('Failed to tell stream position. Resource is closed.');
        }
        $offset = ftell($this->resource);
        if ($offset === false) {
            throw new ResourceInvalidException('Failed to tell stream position. Resource is invalid.');
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
        if (!\is_resource($this->resource)) {
            throw new ResourceClosedException('Failed to seek. Resource is closed.');
        }

        if (!$this->isSeekable()) {
            throw new NotSeekableException('Stream is not seekable');
        }

        if (fseek($this->resource, $offset, $whence) === -1) {
            throw new ResourceInvalidException('Failed to seek stream: Maybe the resource is closed or invalid?');
        }
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
        if (!\is_resource($this->resource)) {
            throw new ResourceClosedException('Failed to write. Resource is closed.');
        }

        if (!$this->isWritable()) {
            throw new NotWritableException('Stream is not writable');
        }

        if (($writtenBytes = fwrite($this->resource, $string)) === false) {
            throw new ResourceInvalidException('Failed to write stream. Resource is invalid.');
        }
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
        if (!\is_resource($this->resource)) {
            throw new ResourceClosedException('Failed to read. Resource is closed.');
        }

        if (!$this->isReadable()) {
            throw new NotReadableException('Stream is not readable');
        }

        if (($content = fread($this->resource, $length)) === false) {
            throw new ResourceInvalidException('Failed to read stream. Resource is invalid.');
        }
        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents(): string
    {
        if (!\is_resource($this->resource)) {
            throw new ResourceClosedException('Failed to write. Resource is closed.');
        }

        if (!$this->isReadable()) {
            throw new NotReadableException('Stream is not readable');
        }

        return stream_get_contents($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
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
     * @throws \RuntimeException
     */
    public function __clone()
    {
        $this->resource = null;
        $this->metadata = null;
        throw new \RuntimeException('Streams cannot be cloned');
    }
}
