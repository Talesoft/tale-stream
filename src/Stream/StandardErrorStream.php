<?php
declare(strict_types=1);

namespace Tale\Stream;

class StandardErrorStream extends FileStream
{
    /**
     * StderrStream constructor.
     */
    public function __construct()
    {
        parent::__construct('php://stderr', 'wb');
    }
}