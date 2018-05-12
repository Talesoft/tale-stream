<?php
declare(strict_types=1);

namespace Tale\Stream;

use Tale\Stream;

class StdinStream extends Stream
{
    /**
     * StderrStream constructor.
     * @param resource|null $stdoutResource
     */
    public function __construct($stdoutResource = null)
    {
        parent::__construct($stdoutResource ?: STDIN);
    }
}
