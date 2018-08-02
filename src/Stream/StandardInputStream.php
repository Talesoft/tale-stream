<?php
declare(strict_types=1);

namespace Tale\Stream;

class StandardInputStream extends FileStream
{
    /**
     * StderrStream constructor.
     */
    public function __construct()
    {
        parent::__construct('php://stdin', 'rb');
    }
}
