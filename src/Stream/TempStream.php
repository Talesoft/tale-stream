<?php
declare(strict_types=1);

namespace Tale\Stream;

use Tale\Stream;

class TempStream extends Stream
{
    public function __construct(?string $mode = null, ?int $maxMemory = null)
    {
        $context = 'php://temp';

        if ($maxMemory) {
            $context .= "/maxmemory:$maxMemory";
        }

        parent::__construct($context, $mode);
    }
}
