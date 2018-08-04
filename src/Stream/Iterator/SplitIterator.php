<?php
declare(strict_types=1);

namespace Tale\Stream\Iterator;

use Psr\Http\Message\StreamInterface;

class SplitIterator extends ReadIterator
{
    /**
     * @var string
     */
    private $delimiter;

    /**
     * ReadIterator constructor.
     * @param StreamInterface $stream
     * @param string $delimiter
     * @param int $chunkSize
     */
    public function __construct(StreamInterface $stream, string $delimiter, int $chunkSize = 2048)
    {
        parent::__construct($stream, $chunkSize);
        $this->delimiter = $delimiter;
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
        foreach (parent::getIterator() as $content) {
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
