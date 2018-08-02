<?php
declare(strict_types=1);

namespace Tale\Stream;

class OutputStream extends FileStream
{
    public function __construct()
    {
        parent::__construct('php://output', 'wb');
    }
}
