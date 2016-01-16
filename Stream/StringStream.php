<?php

namespace Tale\Stream;

use Tale\Stream;

class StringStream extends MemoryStream
{

    public function __construct($initialContent = null, $memoryOnly = true)
    {
        parent::__construct('rb+');

        if ($initialContent) {

            $this->write($initialContent);
            $this->rewind();
        }
    }
}