<?php
declare(strict_types=1);

namespace Tale\Stream;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Tale\Stream;

class Factory implements StreamFactoryInterface
{
    public function createStream(string $content = ''): StreamInterface
    {
        return new TempStream($content);
    }

    public function createStreamFromFile(string $filename, string $mode = 'rb'): StreamInterface
    {
        return new FileStream($filename, $mode);
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }
}
