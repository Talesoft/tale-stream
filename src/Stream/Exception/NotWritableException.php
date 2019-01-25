<?php declare(strict_types=1);

namespace Tale\Stream\Exception;

/**
 * An exception that occurs when an attempt to write to an unwritable stream was made.
 *
 * @package Tale\Stream\Exception
 */
final class NotWritableException extends \RuntimeException implements InvalidOperationExceptionInterface
{
}
