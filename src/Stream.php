<?php
declare(strict_types=1);

namespace Tale;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;

/**
 * Class Stream
 *
 * @package Tale
 */
class Stream implements StreamInterface
{

    /**
     * The default stream mode
     */
    public const DEFAULT_MODE = 'rb+';

    /**
     * The current stream context (file resource)
     *
     * @var resource
     */
    private $context;

    /**
     * An array of meta data information
     *
     * @var array
     */
    private $metadata;


    /**
     * Stream constructor.
     *
     * @param UriInterface|string|resource $context
     * @param string|null $mode
     * @throws \InvalidArgumentException
     */
    public function __construct($context, ?string $mode = null)
    {
        $this->context = $context;
        $mode = $mode ?: self::DEFAULT_MODE;

        if (\is_object($context) && method_exists($context, '__toString')) {
            $this->context = (string)$this->context;
        }

        if (\is_string($this->context)) {
            $this->context = fopen($this->context, $mode);
        }

        if (!\is_resource($this->context)) {
            throw new InvalidArgumentException('Argument 1 needs to be resource or path/URI');
        }

        $this->metadata = stream_get_meta_data($this->context);
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
        if (!$this->context) {
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
        $context = $this->context;
        $this->context = null;
        $this->metadata = null;

        return $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize(): ?int
    {
        if ($this->context === null) {
            return null;
        }

        $stat = fstat($this->context);
        return (int)$stat['size'];
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        return ftell($this->context);
    }

    /**
     * {@inheritdoc}
     */
    public function eof(): bool
    {
        if (!$this->context) {
            return true;
        }

        return feof($this->context);
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable(): bool
    {
        if (!$this->context) {
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

        fseek($this->context, $offset, $whence);
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
        if (!$this->context) {
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

        return fwrite($this->context, $string);
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable(): bool
    {
        if (!$this->context) {
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

        return fread($this->context, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function getContents(): string
    {
        if (!$this->isReadable()) {
            throw new RuntimeException('Stream is not readable');
        }

        return stream_get_contents($this->context);
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
        $this->context = null;
        $this->metadata = null;
        throw new RuntimeException('Streams cannot be cloned');
    }
}
