<?php declare(strict_types=1);

namespace Tale\Stream;

/**
 * A stream that provides the current HTTP response's Body, if available.
 *
 * A wrapper around PHP's php://output stream handle.
 *
 * @package Tale\Stream
 */
final class OutputStream extends FileStream
{
    /**
     * Creates a new output stream instance.
     */
    public function __construct()
    {
        parent::__construct('php://output', 'wb');
    }
}
