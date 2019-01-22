<?php declare(strict_types=1);

namespace Tale\Stream;

/**
 * A stream that provides the current HTTP request's Body, if available.
 *
 * A wrapper around PHP's php://input stream handle.
 *
 * @package Tale\Stream
 */
final class InputStream extends FileStream
{
    /**
     * Creates a new input stream instance.
     */
    public function __construct()
    {
        parent::__construct('php://input', 'rb');
    }
}
