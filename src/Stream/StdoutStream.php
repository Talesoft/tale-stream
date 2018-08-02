<?php
declare(strict_types=1);

namespace Tale\Stream;

class StdoutStream extends FileStream
{
    /**
     * StdoutStream constructor.
     */
    public function __construct()
    {
        parent::__construct('php://stdin', 'wb');
    }
}
