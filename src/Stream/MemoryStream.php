<?php
declare(strict_types=1);

namespace Tale\Stream;

class MemoryStream extends FileStream
{
    public function __construct(string $content = '')
    {
        parent::__construct('php://memory', 'rb+');

        if ($content !== '') {
            $this->write($content);
            $this->rewind();
        }
    }
}
