<?php
declare(strict_types=1);

namespace Tale\Stream;

class InputStream extends FileStream
{
    public function __construct()
    {
        parent::__construct('php://input', 'rb');
    }
}
