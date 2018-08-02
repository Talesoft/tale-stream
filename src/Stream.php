<?php
declare(strict_types=1);

namespace Tale;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * Class Stream
 *
 * @package Tale
 */
class Stream implements StreamInterface
{
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
        if (!$this->resource) {
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
        if ($this->resource === null) {
            return null;
        }

        $stat = fstat($this->resource);
        return (int)$stat['size'];
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        return ftell($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function eof(): bool
    {
        if (!$this->resource) {
            return true;
        }

        return feof($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable(): bool
    {
        if (!$this->resource) {
            return false;
        }

        return $this->getMetadata('seekable') ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = \SEEK_SET): bool
    {
        if (!$this->isSeekable()) {
            throw new RuntimeException('Stream is not seekable');
        }

        fseek($this->resource, $offset, $whence);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): bool
    {
        return $this->seek(0);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable(): bool
    {
        if (!$this->resource) {
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
    public function write($string)
    {
        if (!$this->isWritable()) {
            throw new RuntimeException('Stream is not writable');
        }

        return fwrite($this->resource, $string);
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable(): bool
    {
        if (!$this->resource) {
            return false;
        }

        $mode = $this->getMetadata('mode');
        return (strpos($mode, 'r') !== false  || strpos($mode, '+') !== false );
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        if (!$this->isReadable()) {
            throw new RuntimeException('Stream is not readable');
        }

        return fread($this->resource, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function getContents(): string
    {
        if (!$this->isReadable()) {
            throw new RuntimeException('Stream is not readable');
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
    public function __toString()
    {
        try {
            if ($this->isReadable() && $this->isSeekable()) {
                $this->rewind();
            }
            return $this->getContents();
        } catch (\Exception $ex) {
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
        throw new RuntimeException('Streams cannot be cloned');
    }
}
