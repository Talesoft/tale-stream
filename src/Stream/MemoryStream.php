<?php
declare(strict_types=1);

namespace Tale\Stream;

use Tale\Stream;

class MemoryStream extends Stream
{
    public function __construct(?string $mode = null)
    {
        parent::__construct('php://memory', $mode);
    }
}
