<?php

namespace Tale\Stream;

use Tale\Stream;

class InputStream extends Stream
{

    public function __construct()
    {

        parent::__construct('php://input', 'rb');
    }
}