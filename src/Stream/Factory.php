<?php declare(strict_types=1);

namespace Tale\Stream;

use Psr\Http\Message\StreamFactoryInterface;

/**
 * A basic factory implementation to create some stream instances.
 *
 * Inject a Psr\Http\Message\StreamFactoryInterface to get this instance
 * in your DI container.
 *
 * @package Tale\Stream
 */
final class Factory implements StreamFactoryInterface
{
    use FactoryTrait;
}
