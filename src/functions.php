<?php declare(strict_types=1);

namespace Tale;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Tale\Stream\Iterator\LineIterator;
use Tale\Stream\Iterator\ReadIterator;
use Tale\Stream\Iterator\SplitIterator;
use Tale\Stream\Iterator\WriteIterator;
use Tale\Stream\NullStream;

function stream($resource): StreamInterface
{
    return new Stream($resource);
}

function stream_factory(): StreamFactoryInterface
{
    return new StreamFactory();
}

function stream_file(
    string $filename,
    string $mode = 'rb',
    ?bool $useIncludePath = false,
    $context = null
): StreamInterface {

    return Stream::createFileStream($filename, $mode, $useIncludePath, $context);
}

function stream_input(): StreamInterface
{
    return Stream::createInputStream();
}

function stream_output(): StreamInterface
{
    return Stream::createOutputStream();
}

function stream_memory(string $content = ''): StreamInterface
{
    return Stream::createMemoryStream($content);
}

function stream_temp(string $content = '', ?int $maxMemory = null): StreamInterface
{
    return Stream::createTempStream($content, $maxMemory);
}

function stream_stdin(): StreamInterface
{
    return Stream::createStdinStream();
}

function stream_stderr(): StreamInterface
{
    return Stream::createStderrStream();
}

function stream_stdout(): StreamInterface
{
    return Stream::createStderrStream();
}

function stream_null(): StreamInterface
{
    return new NullStream();
}

function stream_iterator_line(ReadIterator $readIterator, string $delimiter = LineIterator::DELIMITER_LF): LineIterator
{
    return new LineIterator($readIterator, $delimiter);
}

function stream_get_lines(
    StreamInterface $stream,
    string $delimiter = LineIterator::DELIMITER_LF,
    int $chunkSize = 1024
): \Generator {
    yield from stream_iterator_line(stream_iterator_read($stream, $chunkSize), $delimiter);
}

function stream_iterator_read(StreamInterface $stream, int $chunkSize = 1024): ReadIterator
{
    return new ReadIterator($stream, $chunkSize);
}

function stream_iterator_split(ReadIterator $readIterator, string $delimiter): SplitIterator
{
    return new SplitIterator($readIterator, $delimiter);
}

function stream_split(
    StreamInterface $stream,
    string $delimiter,
    int $chunkSize = 1024
): \Generator {
    yield from stream_iterator_split(stream_iterator_read($stream, $chunkSize), $delimiter);
}

function stream_iterator_write(StreamInterface $stream, iterable $sourceIterable): WriteIterator
{
    return new WriteIterator($stream, $sourceIterable);
}

function stream_iterator_write_all(StreamInterface $stream, iterable $sourceIterable): int
{
    return stream_iterator_write($stream, $sourceIterable)->writeAll();
}

function stream_pipe(StreamInterface $inputStream, StreamInterface $outputStream, int $chunkSize = 1024): WriteIterator
{
    return stream_iterator_write($outputStream, stream_iterator_read($inputStream, $chunkSize));
}

function stream_pipe_all(StreamInterface $inputStream, StreamInterface $outputStream, int $chunkSize = 1024): int
{
    return stream_pipe($inputStream, $outputStream, $chunkSize)->writeAll();
}
