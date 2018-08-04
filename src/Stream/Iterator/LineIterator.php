<?php
declare(strict_types=1);

namespace Tale\Stream\Iterator;

use Psr\Http\Message\StreamInterface;

class LineIterator extends SplitIterator
{
    public const DELIMITER_CR = "\r";
    public const DELIMITER_LF = "\n";
    public const DELIMITER_CRLF = "\r\n";

    /**
     * ReadIterator constructor.
     * @param StreamInterface $stream
     * @param string $delimiter
     * @param int $chunkSize
     */
    public function __construct(StreamInterface $stream, string $delimiter = self::DELIMITER_LF, int $chunkSize = 2048)
    {
        parent::__construct($stream, $delimiter, $chunkSize);
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
