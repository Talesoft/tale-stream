<?php declare(strict_types=1);

namespace Tale\Stream;

/**
 * A stream that provides underlying STDERR stream of the PHP process.
 *
 * A wrapper around PHP's php://stderr stream handle.
 *
 * @package Tale\Stream
 */
final class StandardErrorStream extends FileStream
{
    /**
     * Creates a new standard error stream instance.
     */
    public function __construct()
    {
        parent::__construct('php://stderr', 'wb');
    }
}
