<?php

namespace Tale\Stream;

use Tale\Stream;

class TempStream extends Stream
{

    public function __construct($mode = null, $maxMemory = null)
    {

        $context = 'php://temp';

        if ($maxMemory)
            $context .= "/maxmemory:$maxMemory";

        parent::__construct($context, $mode);
    }
}