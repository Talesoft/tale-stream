<?php declare(strict_types=1);

namespace Tale\Stream;

use Psr\Http\Message\StreamInterface;
use Tale\Stream;

/**
 * A trait to implement a fully PSR-17 compatible StreamFactory.
 *
 * Implement Psr\Http\Message\StreamFactoryInterface and add this trait to create
 * a PSR-17 compatible stream factory on any class you like.
 *
 * @package Tale\Stream
 */
trait FactoryTrait
{
    final public function createStream(string $content = ''): StreamInterface
    {
        return new TempStream($content);
    }

    final public function createStreamFromFile(string $filename, string $mode = 'rb'): StreamInterface
    {
        return new FileStream($filename, $mode);
    }

    final public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }
}
