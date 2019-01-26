<?php declare(strict_types=1);

namespace Tale;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * A basic factory implementation to create some stream instances.
 *
 * Inject a Psr\Http\Message\StreamFactoryInterface to get this instance
 * in your DI container.
 *
 * @package Tale
 */
final class StreamFactory implements StreamFactoryInterface
{
    public function createStream(string $content = ''): StreamInterface
    {
        return Stream::createMemoryStream($content);
    }

    public function createStreamFromFile(string $filename, string $mode = 'rb'): StreamInterface
    {
        return Stream::createFileStream($filename, $mode);
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }
}
