<?php
declare(strict_types=1);

namespace Tale\Stream;

use Tale\Stream;

class FileStream extends Stream
{
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
