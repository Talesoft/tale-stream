<?php declare(strict_types=1);

namespace Tale\Stream\Iterator;

use Generator;

/**
 * An iterator that reads a stream line by line based on a specified line delimiter.
 *
 * @package Tale\Stream\Iterator
 */
final class LineIterator implements \IteratorAggregate
{
    /**
     * A Carriage Return delimiter (CR, \r).
     */
    public const DELIMITER_CR = "\r";

    /**
     * A Line Feed delimiter (LF, \n) commonly found on UNIX systems.
     */
    public const DELIMITER_LF = "\n";

    /**
     * A Carriage Return/Line Feed delimiter (CRLF, \r\n) commonly
     * found on windows systems.
     */
    public const DELIMITER_CRLF = "\r\n";

    /**
     * The ReadIterator that we're reading line by line.
     *
     * @var ReadIterator
     */
    private ReadIterator $readIterator;

    /**
     * The delimiter of our lines.
     *
     * @var string
     */
    private string $delimiter;

    /**
     * Creates a new line iterator instance.
     *
     * Notice that other than with fgets() or fgetcsv(), the line length is not limited
     * by the chunk size through the way the extended SplitIterator works.
     *
     * This iterator will also try to normalize the lines, so Windows files read
     * with a single LF delimiter will still end up without CR delimiters
     *
     * @param ReadIterator $readIterator A ReadIterator to read line by line
     * @param string $delimiter The character that lines are separated with (See DELIMITER_* constants, default: \n)
     */
    public function __construct(ReadIterator $readIterator, string $delimiter = self::DELIMITER_LF)
    {
        $this->readIterator = $readIterator;
        $this->delimiter = $delimiter;
    }

    /**
     * Returns the ReadIterator we're reading line by line.
     *
     * @return ReadIterator
     */
    public function getReadIterator(): ReadIterator
    {
        return $this->readIterator;
    }

    /**
     * Returns the delimiter of our lines.
     *
     * @return string
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * @return Generator<string>
     */
    public function getIterator(): Generator
    {
        $iterator = new SplitIterator($this->readIterator, $this->delimiter);
        foreach ($iterator as $content) {
            yield trim($content, self::DELIMITER_CRLF);
        }
    }
}
