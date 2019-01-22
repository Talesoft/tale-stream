<?php declare(strict_types=1);

namespace Tale\Stream;

use Tale\Stream;

/**
 * A file stream wrapper that wraps PHP's fopen() function.
 *
 * @package Tale\Stream
 */
class FileStream extends Stream
{
    /**
     * Creates a new stream using PHP's fopen() function.
     *
     * @see https://php.net/fopen
     *
     * @param string $filename The path or URI to the file you want to open.
     * @param string $mode The mode to open the file with.
     * @param bool $useIncludePath Use the include path to find files.
     * @param null $context A custom configured stream context.
     */
    public function __construct(string $filename, string $mode, bool $useIncludePath = false, $context = null)
    {
        if ($context !== null && !\is_resource($context)) {
            throw new \InvalidArgumentException(sprintf(
                'Argument 4 passed to %s->__construct needs to be resource, %s given',
                static::class,
                \gettype($context)
            ));
        }
        parent::__construct($context !== null
            ? fopen($filename, $mode, $useIncludePath, $context)
            : fopen($filename, $mode, $useIncludePath));
    }
}
