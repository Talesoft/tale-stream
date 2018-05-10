<?php
declare(strict_types=1);

namespace Tale\Stream;

class StringStream extends MemoryStream
{
    public function __construct(?string $initialContent = null, ?string $mode = null)
    {
        parent::__construct($mode);

        if ($initialContent !== null) {
            $this->write($initialContent);
            $this->rewind();
        }
    }
}
