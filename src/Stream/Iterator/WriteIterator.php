<?php declare(strict_types=1);

namespace Tale\Stream\Iterator;

use Psr\Http\Message\StreamInterface;
use Tale\Stream\Exception\NotWritableException;

/**
 * An iterator implementation that will write an inner iterator to a passed stream upon iteration.
 *
 * @package Tale\Stream\Iterator
 */
final class WriteIterator implements \IteratorAggregate
{
    /**
     * The stream instance we write to.
     *
     * @var StreamInterface
     */
    private $stream;

    /**
     * The iterable to read from.
     *
     * @var iterable
     */
    private $sourceIterable;

    /**
     * Creates a new write iterator instance.
     *
     * @param StreamInterface $stream A writable stream instance to write to.
     * @param iterable $sourceIterable An iterable to read from.
     */
    public function __construct(StreamInterface $stream, iterable $sourceIterable)
    {
        if (!$stream->isWritable()) {
            throw new NotWritableException('Stream is not writable');
        }
        $this->stream = $stream;
        $this->sourceIterable = $sourceIterable;
    }

    /**
     * Returns the stream instance we write to.
     *
     * @return StreamInterface
     */
    public function getStream(): StreamInterface
    {
        return $this->stream;
    }

    /**
     * Returns to iterable that is read from.
     *
     * @return iterable
     */
    public function getSourceIterable(): iterable
    {
        return $this->sourceIterable;
    }

    /**
     * Generates integer values for the amount of written bytes in each iteration.
     *
     * @return \Generator
     */
    public function getIterator(): \Generator
    {
        foreach ($this->sourceIterable as $content) {
            try {
                yield $this->stream->write($content);
            } catch (\InvalidArgumentException $ex) {
                throw new \RuntimeException('The iterable generated values that are not writable', 0, $ex);
            }
        }
    }

    /**
     * Completely finished the iteration and returns the total amount of bytes written.
     *
     * @return int
     */
    public function writeAll(): int
    {
        return array_sum(iterator_to_array($this->getIterator()));
    }
}
