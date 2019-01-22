<?php declare(strict_types=1);

namespace Tale\Stream\Iterator;

use Psr\Http\Message\StreamInterface;
use Tale\Stream\Exception\NotReadableException;

/**
 * An iterator that will read a stream sequentially based on a chunk size.
 *
 * @package Tale\Stream\Iterator
 */
final class ReadIterator implements \IteratorAggregate
{
    /**
     * The stream that is read from.
     *
     * @var StreamInterface
     */
    private $stream;

    /**
     * The chunk size to read for every iteration.
     *
     * @var int
     */
    private $chunkSize;

    /**
     * Creates a new read iterator.
     *
     * @param StreamInterface $stream The stream instance to read from.
     * @param int $chunkSize The chunk size to use for each iteration (Default: 1024)
     */
    public function __construct(StreamInterface $stream, int $chunkSize = 1024)
    {
        if (!$stream->isReadable()) {
            throw new NotReadableException('Stream is not readable');
        }
        $this->stream = $stream;
        $this->chunkSize = $chunkSize;
    }

    /**
     * Returns the stream instance we read from.
     *
     * @return StreamInterface
     */
    public function getStream(): StreamInterface
    {
        return $this->stream;
    }

    /**
     * Returns the chunk size used in each iteration.
     *
     * @return int
     */
    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    /**
     * Checks if the stream we read from is at its end.
     *
     * @return bool
     */
    public function eof(): bool
    {
        return $this->stream->eof();
    }

    /**
     * Generates chunks based on the passed chunk size through reading from the stream.
     *
     * @return \Generator
     */
    public function getIterator(): \Generator
    {
        while (!$this->stream->eof()) {
            $item = $this->stream->read($this->chunkSize);
            // @codeCoverageIgnoreStart
            //I don't know how to fabricate this manually, so I can't test it.
            if ($item === '') {
                continue;
            }
            // @codeCoverageIgnoreEnd
            yield $item;
        }
    }
}
