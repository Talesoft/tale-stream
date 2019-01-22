<?php declare(strict_types=1);

namespace Tale\Stream;

/**
 * A stream that provides underlying STDOUT stream of the PHP process.
 *
 * A wrapper around PHP's php://stdout stream handle.
 *
 * @package Tale\Stream
 */
final class StandardOutputStream extends FileStream
{
    /**
     * Creates a new standard output stream instance.
     */
    public function __construct()
    {
        parent::__construct('php://stdout', 'wb');
    }
}
