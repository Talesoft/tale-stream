<?php declare(strict_types=1);

namespace Tale\Stream\Iterator;

use Generator;

/**
 * An iterator that takes any iterable and splits its full content by a delimiter.
 *
 * A ReadIterator yielding
 *
 *     ["line 1\nline", " 2\nline 3"]
 *
 * will end up in this iterator yielding
 *
 *     ["line 1", "line 2", "line 3"]
 *
 * @package Tale\Stream\Iterator
 */
final class SplitIterator implements \IteratorAggregate
{
    /**
     * The ReadIterator that we're splitting up.
     *
     * @var ReadIterator
     */
    private ReadIterator $readIterator;

    /**
     * The delimiter we split up by.
     *
     * @var string
     */
    private string $delimiter;

    /**
     * Creates a new split iterator instance.
     *
     * @param ReadIterator $readIterator The ReadIterator to split up.
     * @param string $delimiter The delimiter to split up by.
     */
    public function __construct(ReadIterator $readIterator, string $delimiter)
    {
        $this->readIterator = $readIterator;
        $this->delimiter = $delimiter;
    }

    /**
     * Returns the ReadIterator we're splitting up.
     *
     * @return ReadIterator
     */
    public function getReadIterator(): ReadIterator
    {
        return $this->readIterator;
    }

    /**
     * Returns the delimiter we're splitting up by.
     *
     * @return string
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * Generates parts of an iterator split by the specified delimiter.
     *
     * @return Generator<string>
     */
    public function getIterator(): Generator
    {
        $line = '';
        foreach ($this->readIterator as $content) {
            $parts = explode($this->delimiter, $content);
            $partCount = \count($parts);
            if ($partCount > 1) {
                foreach ($parts as $i => $part) {
                    if ($i === 0) {
                        yield $line.$part;
                        continue;
                    }

                    if ($i === $partCount - 1) {
                        $line = $part;
                        continue;
                    }
                    yield $part;
                }
                continue;
            }
            $line .= $content;
        }
        yield $line;
    }
}
