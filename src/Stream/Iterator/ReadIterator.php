<?php
declare(strict_types=1);

namespace Tale\Stream\Iterator;

use Psr\Http\Message\StreamInterface;
use Tale\Stream\Exception\NotReadableException;

class ReadIterator implements \IteratorAggregate
{
    private $stream;
    private $chunkSize;

    /**
     * ReadIterator constructor.
     * @param StreamInterface $stream
     * @param int $chunkSize
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
     * @return StreamInterface
     */
    public function getStream(): StreamInterface
    {
        return $this->stream;
    }

    /**
     * @return int
     */
    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    /**
     * @param int $chunkSize
     * @return $this
     */
    public function setChunkSize(int $chunkSize): self
    {
        $this->chunkSize = $chunkSize;
        return $this;
    }

    public function eof(): bool
    {
        return $this->stream->eof();
    }

    public function rewind(): void
    {
        $this->stream->rewind();
    }

    public function getIterator(): \Generator
    {
        while (!$this->stream->eof()) {
            $item = $this->stream->read($this->chunkSize);
            if ($item === '') {
                continue;
            }
            yield $item;
        }
    }
}
