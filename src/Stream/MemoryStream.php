<?php declare(strict_types=1);

namespace Tale\Stream;

/**
 * A stream that only resides in memory and is gone after execution.
 *
 * A wrapper around PHP's php://memory stream handle.
 *
 * @package Tale\Stream
 */
final class MemoryStream extends FileStream
{
    /**
     * Creates a new memory stream instance.
     *
     * @param string $content The initial content to fill the stream with.
     */
    public function __construct(string $content = '')
    {
        parent::__construct('php://memory', 'rb+');

        if ($content !== '') {
            $this->write($content);
            $this->rewind();
        }
    }
}
