<?php declare(strict_types=1);

namespace Tale\Stream\Exception;

use RuntimeException;

/**
 * An exception that occurs when operations on a closed resource were attempted.
 *
 * @package Tale\Stream\Exception
 */
final class ResourceClosedException extends RuntimeException
{
}
