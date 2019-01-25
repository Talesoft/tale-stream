<?php declare(strict_types=1);

namespace Tale\Stream\Exception;

/**
 * An exception that occurs when an attempt to seek an unseekable stream was made.
 *
 * @package Tale\Stream\Exception
 */
final class NotSeekableException extends \RuntimeException implements InvalidOperationExceptionInterface
{
}
