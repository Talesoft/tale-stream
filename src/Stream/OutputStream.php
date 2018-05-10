<?php
declare(strict_types=1);

namespace Tale\Stream;

use Tale\Stream;

class OutputStream extends Stream
{
    public function __construct()
    {
        parent::__construct('php://output', 'wb');
    }
}