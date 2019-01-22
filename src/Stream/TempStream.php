<?php declare(strict_types=1);

namespace Tale\Stream;

/**
 * A stream that only resides in memory and is gone after execution.
 *
 * Other than the MemoryStream, if the content is becoming too large,
 * this stream will start paging content in temporary files.
 *
 * A wrapper around PHP's php://temp stream handle.
 *
 * @package Tale\Stream
 */
final class TempStream extends FileStream
{
    /**
     * Creates a new memory stream instance.
     *
     * @param string $content The initial content to fill the stream with.
     * @param int|null $maxMemory If passed, starts paging to temp files after the memory limit.
     */
    public function __construct(string $content = '', int $maxMemory = null)
    {
        parent::__construct('php://temp'.($maxMemory !== null ? "/maxmemory:{$maxMemory}" : ''), 'rb+');

        if ($content !== '') {
            $this->write($content);
            $this->rewind();
        }
    }
}
