<?php declare(strict_types=1);

namespace Tale;

use Generator;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Tale\Stream\Iterator\LineIterator;
use Tale\Stream\Iterator\ReadIterator;
use Tale\Stream\Iterator\SplitIterator;
use Tale\Stream\Iterator\WriteIterator;
use Tale\Stream\NullStream;

/**
 * Creates a new Stream instance from a PHP stream resource.
 *
 * @param $resource
 * @return StreamInterface
 */
function stream($resource): StreamInterface
{
    return new Stream($resource);
}

/**
 * Creates a new stream factory to easily create streams in a centralized manner.
 *
 * @return StreamFactoryInterface
 */
function stream_factory(): StreamFactoryInterface
{
    return new StreamFactory();
}

/**
 * Creates a new File Stream given a file path.
 *
 * This function works very similar to fopen(), but it will simply return a valid Stream instance.
 *
 * @param string $filename The path to a file or stream wrapper resource.
 * @param string $mode The mode to open the file with (Same as fopen() mode).
 * @param bool|null $useIncludePath Should we use the include_path or not?
 * @param null $context A stream context (Same as fopen() stream context)
 * @return StreamInterface
 */
function stream_file(
    string $filename,
    string $mode = 'rb',
    ?bool $useIncludePath = false,
    $context = null
): StreamInterface {

    return Stream::createFileStream($filename, $mode, $useIncludePath, $context);
}

/**
 * Creates a new Input Stream.
 *
 * This uses the php://input stream wrapper.
 * Use this to read the HTTP POST/PUT body from requests.
 *
 * @return StreamInterface
 */
function stream_input(): StreamInterface
{
    return Stream::createInputStream();
}

/**
 * Creates a new Output Stream.
 *
 * This uses the php://output stream wrapper.
 * Use this to write to HTTP responses.
 *
 * @return StreamInterface
 */
function stream_output(): StreamInterface
{
    return Stream::createOutputStream();
}

/**
 * Creates a new memory stream you can read from and write to freely.
 *
 * This uses the php://memory stream wrapper.
 * You can pass it initial content and it will always start at position 0.
 *
 * @param string $content Initial content to fill the stream with.
 * @return StreamInterface
 */
function stream_memory(string $content = ''): StreamInterface
{
    return Stream::createMemoryStream($content);
}

/**
 * Creates a new temporary stream you can read from and write to freely.
 *
 * The stream will use your memory unless you pass the second parameter, then it
 * will swap to a file after the maximum memory is reached.
 *
 * This uses the php://temp stream wrapper.
 * You can pass it initial content and it will always start at position 0.
 *
 * @param string $content Initial content to fill the stream with.
 * @param int|null $maxMemory Amount of bytes when the stream will start swapping to a file.
 * @return StreamInterface
 */
function stream_temp(string $content = '', ?int $maxMemory = null): StreamInterface
{
    return Stream::createTempStream($content, $maxMemory);
}

/**
 * @return StreamInterface
 */
function stream_stdin(): StreamInterface
{
    return Stream::createStdinStream();
}

/**
 * @return StreamInterface
 */
function stream_stderr(): StreamInterface
{
    return Stream::createStderrStream();
}

/**
 * @return StreamInterface
 */
function stream_stdout(): StreamInterface
{
    return Stream::createStderrStream();
}

/**
 * @return StreamInterface
 */
function stream_null(): StreamInterface
{
    return new NullStream();
}

/**
 * Creates a read iterator for a stream that will read a stream chunk by chunk.
 *
 * You can iterate it and every item will be a string exactly as long as the passed chunk size.
 * Useful to read streams with regards to memory and/or with iterators.
 *
 * @param StreamInterface $stream The stream to read chunks from.
 * @param int $chunkSize The chunk size (Default: 1024)
 * @return ReadIterator
 */
function stream_read(StreamInterface $stream, int $chunkSize = 1024): ReadIterator
{
    return new ReadIterator($stream, $chunkSize);
}

/**
 * Creates a new line iterator for streams.
 *
 * You can iterate it and it will read the stream line by line.
 * It has no soft limit on line length.
 *
 * @param ReadIterator $readIterator A read iterator to read from.
 * @param string $delimiter A delimiter to split lines by (Default: "\n")
 * @return LineIterator
 */
function stream_read_lines(ReadIterator $readIterator, string $delimiter = LineIterator::DELIMITER_LF): LineIterator
{
    return new LineIterator($readIterator, $delimiter);
}

/**
 * Returns an iterator that contains all lines in your stream.
 *
 * You can iterate it and it will read the stream line by line lazily.
 * It has no soft limit on line length.
 *
 * @param StreamInterface $stream The stream to read lines of.
 * @param string $delimiter A delimiter to split lines by (Default: "\n")
 * @param int $chunkSize The chunk size to use (more = faster, but more memory usage) (Default: 1024)
 * @return Generator<string>
 */
function stream_get_lines(
    StreamInterface $stream,
    string $delimiter = LineIterator::DELIMITER_LF,
    int $chunkSize = 1024
): Generator {
    yield from stream_read_lines(stream_read($stream, $chunkSize), $delimiter);
}

/**
 * Creates a split iterator that will read a stream part by part.
 *
 * You can pass it a delimiter to split lines with.
 * It has no soft limit on length of the part.
 *
 * @param ReadIterator $readIterator A read iterator to read from.
 * @param string $delimiter A delimiter to split parts by
 * @return SplitIterator
 */
function stream_read_split(ReadIterator $readIterator, string $delimiter): SplitIterator
{
    return new SplitIterator($readIterator, $delimiter);
}

/**
 * Splits a stream by a delimiter and returns an iterator containing the parts.
 *
 * Basically explode() for PSR streams.
 * You can iterate it and it will read the stream part by part lazily.
 * It has no hard limit on part length.
 *
 * @param StreamInterface $stream The stream to read chunks from.
 * @param string $delimiter A delimiter to split parts by
 * @param int $chunkSize The chunk size (Default: 1024)
 * @return Generator<string>
 */
function stream_split(
    StreamInterface $stream,
    string $delimiter,
    int $chunkSize = 1024
): Generator {
    yield from stream_read_split(stream_read($stream, $chunkSize), $delimiter);
}

/**
 * Creates a write iterator that will write an iterable to a stream when it is iterated itself.
 *
 * This is useful for piping streams in a memory efficient manner.
 * You can pipe streams by passing a ReadIterator of a stream as the second parameter.
 *
 * Don't forget to iterate the iterator or nothing gets written (You can also use ->writeAll() on it).
 *
 * @param StreamInterface $stream The stream to write to.
 * @param iterable $sourceIterable The iterable to read from.
 * @return WriteIterator
 */
function stream_write(StreamInterface $stream, iterable $sourceIterable): WriteIterator
{
    return new WriteIterator($stream, $sourceIterable);
}

/**
 * Writes an iterable to a stream.
 *
 * It will iterate the passed iterable and write it to the stream item by item.
 *
 * It will return the total amount of written bytes.
 *
 * @param StreamInterface $stream The stream to write to.
 * @param iterable $sourceIterable The iterable to read from.
 * @return int
 */
function stream_write_all(StreamInterface $stream, iterable $sourceIterable): int
{
    return stream_write($stream, $sourceIterable)->writeAll();
}

/**
 * Creates a write iterator that will write one stream to another (piping) once iterated.
 *
 * Don't forget to iterate the iterator or nothing gets written (You can also use ->writeAll() on it).
 *
 * @param StreamInterface $inputStream The stream to write to.
 * @param StreamInterface $outputStream The stream to read from.
 * @param int $chunkSize The chunk size to use (higher = faster but more memory) (Default: 1024)
 * @return WriteIterator
 */
function stream_pipe(StreamInterface $inputStream, StreamInterface $outputStream, int $chunkSize = 1024): WriteIterator
{
    return stream_write($outputStream, stream_read($inputStream, $chunkSize));
}

/**
 * Writes one stream to another (piping).
 *
 * @param StreamInterface $inputStream The stream to write to.
 * @param StreamInterface $outputStream The stream to read from.
 * @param int $chunkSize The chunk size to use (higher = faster but more memory) (Default: 1024)
 * @return int
 */
function stream_pipe_all(StreamInterface $inputStream, StreamInterface $outputStream, int $chunkSize = 1024): int
{
    return stream_pipe($inputStream, $outputStream, $chunkSize)->writeAll();
}
