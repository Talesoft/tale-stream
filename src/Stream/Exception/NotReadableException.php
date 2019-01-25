<?php declare(strict_types=1);

namespace Tale\Stream\Exception;

/**
 * An exception that occurs when an attempt to read an unreadable stream was made.
 *
 * @package Tale\Stream\Exception
 */
final class NotReadableException extends \RuntimeException implements InvalidOperationExceptionInterface
{
}
