<?php
declare(strict_types=1);

namespace Tale\Stream\Iterator;

use Psr\Http\Message\StreamInterface;
use Tale\Stream\Exception\NotReadableException;

class WriteIterator implements \IteratorAggregate
{
    private $stream;
    private $sourceIterator;

    /**
     * ReadIterator constructor.
     * @param iterable $sourceIterator
     * @param StreamInterface $stream
     */
    public function __construct(StreamInterface $stream, iterable $sourceIterator)
    {
        if (!$stream->isWritable()) {
            throw new NotReadableException('Stream is not writable');
        }
        $this->stream = $stream;
        $this->sourceIterator = $sourceIterator;
    }

    /**
     * @return StreamInterface
     */
    public function getStream(): StreamInterface
    {
        return $this->stream;
    }

    /**
     * @return iterable
     */
    public function getSourceIterator(): iterable
    {
        return $this->sourceIterator;
    }

    public function rewind(): void
    {
        $this->stream->rewind();
    }

    public function getIterator(): \Generator
    {
        foreach ($this->sourceIterator as $content) {
            yield $this->stream->write($content);
        }
    }
}
