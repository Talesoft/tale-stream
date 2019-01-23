<?php declare(strict_types=1);

namespace Tale;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Tale\Stream\Factory;

function stream($resource): StreamInterface
{
    return new Stream($resource);
}

function stream_factory(): StreamFactoryInterface
{
    return new Factory();
}

function stream_create_file(
    string $filename,
    string $mode = 'rb',
    ?bool $useIncludePath = null,
    $context = null
): StreamInterface {

    return Stream::createFileStream($filename, $mode, $useIncludePath, $context);
}

function stream_create_input(): StreamInterface
{
    return Stream::createInputStream();
}

function stream_create_output(): StreamInterface
{
    return Stream::createOutputStream();
}

function stream_create_memory(string $content = ''): StreamInterface
{
    return Stream::createMemoryStream($content);
}

function stream_create_temp(string $content = '', ?int $maxMemory = null): StreamInterface
{
    return Stream::createTempStream($content, $maxMemory);
}

function stream_create_stdin(): StreamInterface
{
    return Stream::createStdinStream();
}

function stream_create_stderr(): StreamInterface
{
    return Stream::createStderrStream();
}

function stream_create_stdout(): StreamInterface
{
    return Stream::createStderrStream();
}
