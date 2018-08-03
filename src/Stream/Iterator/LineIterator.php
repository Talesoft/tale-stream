<?php
declare(strict_types=1);

namespace Tale\Stream\Iterator;

class LineIterator extends SplitIterator
{
    public const DELIMITER_CR = "\r";
    public const DELIMITER_LF = "\n";
    public const DELIMITER_CRLF = "\r\n";
    /**
     * ReadIterator constructor.
     * @param ReadIterator $readIterator
     * @param string $delimiter
     */
    public function __construct(ReadIterator $readIterator, string $delimiter = self::DELIMITER_LF)
    {
        parent::__construct($readIterator, $delimiter);
    }

    /**
     * @return \Generator|string[]
     */
    public function getIterator(): \Generator
    {
        foreach (parent::getIterator() as $content) {
            yield trim($content, self::DELIMITER_CRLF);
        }
    }
}