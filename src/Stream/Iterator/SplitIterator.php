<?php
declare(strict_types=1);

namespace Tale\Stream\Iterator;

class SplitIterator implements \IteratorAggregate
{
    private $readIterator;
    private $delimiter;

    /**
     * ReadIterator constructor.
     * @param ReadIterator $readIterator
     * @param string $delimiter
     */
    public function __construct(ReadIterator $readIterator, string $delimiter)
    {
        $this->readIterator = $readIterator;
        $this->delimiter = $delimiter;
    }

    /**
     * @return ReadIterator
     */
    public function getReadIterator(): ReadIterator
    {
        return $this->readIterator;
    }

    /**
     * @return string
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     * @return $this
     */
    public function setDelimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    /**
     * @return \Generator|string[]
     */
    public function getIterator(): \Generator
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
