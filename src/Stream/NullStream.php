<?php
declare(strict_types=1);

namespace Tale\Stream;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * Class Stream
 *
 * @package Tale
 */
class NullStream implements StreamInterface
{
    /**
     * {@inheritdoc}
     */
    public function close(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize(): ?int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function eof(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = \SEEK_SET): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string): int
    {
        return \strlen($string);
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return '';
    }

    /**
     * @throws \RuntimeException
     */
    public function __clone()
    {
        throw new RuntimeException('Streams cannot be cloned');
    }
}