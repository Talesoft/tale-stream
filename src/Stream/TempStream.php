<?php
declare(strict_types=1);

namespace Tale\Stream;

class TempStream extends FileStream
{
    public function __construct(string $content = '', int $maxMemory = null)
    {
        $context = 'php://temp';

        if ($maxMemory !== null) {
            $context .= "/maxmemory:$maxMemory";
        }

        parent::__construct($context, 'rb+');

        if ($content !== '') {
            $this->write($content);
            $this->rewind();
        }
    }
}
