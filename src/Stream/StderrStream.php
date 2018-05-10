<?php
declare(strict_types=1);

namespace Tale\Stream;

use Tale\Stream;

class StderrStream extends Stream
{
    public function __construct()
    {
        parent::__construct(STDERR);
    }
}
