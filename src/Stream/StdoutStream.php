<?php
declare(strict_types=1);

namespace Tale\Stream;

use Tale\Stream;

class StdoutStream extends Stream
{
    public function __construct()
    {
        parent::__construct(STDOUT);
    }
}
