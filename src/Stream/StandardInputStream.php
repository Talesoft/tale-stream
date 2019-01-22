<?php declare(strict_types=1);

namespace Tale\Stream;

/**
 * A stream that provides underlying STDIN stream of the PHP process.
 *
 * A wrapper around PHP's php://stdin stream handle.
 *
 * @package Tale\Stream
 */
final class StandardInputStream extends FileStream
{
    /**
     * Creates a new standard input stream instance.
     */
    public function __construct()
    {
        parent::__construct('php://stdin', 'rb');
    }
}
