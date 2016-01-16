<?php

namespace Tale\Stream;

use Tale\Stream;

class MemoryStream extends Stream
{

    public function __construct($mode = null)
    {

        parent::__construct('php://memory', $mode);
    }
}