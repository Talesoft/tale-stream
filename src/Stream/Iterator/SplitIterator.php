<?php
declare(strict_types=1);

namespace Tale\Stream\Iterator;

use Tale\Stream;

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
     * @return SplitIterator
     */
    public function setDelimiter(string $delimiter): SplitIterator
    {
        $this->delimiter = $delimiter;
        return $this;
    }


    /**
     * @return \Generator|string[]
     */
    public function getIterator(): \Generator
    {
        $line = null;
        $delimLen = \strlen($this->delimiter);
        $stream = $this->readIterator->getStream();
        foreach ($this->readIterator as $content) {
            $pos = strpos($content, $this->delimiter);

            if ($line === null) {
                $line = '';
            }

            if ($pos === -1) {
                $line .= $content;
                continue;
            }

            $restLen = \strlen($content) - ($pos + $delimLen);
            $stream->seek(-$restLen, Stream::SEEK_CURRENT);
            $line .= substr($content, 0, $pos);
            yield $line;
            $line = null;
        }

        if ($line !== null) {
            yield $line;
        }
    }
}