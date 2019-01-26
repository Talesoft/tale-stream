
[![Packagist](https://img.shields.io/packagist/v/talesoft/tale-stream.svg?style=for-the-badge)](https://packagist.org/packages/talesoft/tale-stream)
[![License](https://img.shields.io/github/license/Talesoft/tale-stream.svg?style=for-the-badge)](https://github.com/Talesoft/tale-stream/blob/master/LICENSE.md)
[![CI](https://img.shields.io/travis/Talesoft/tale-stream.svg?style=for-the-badge)](https://travis-ci.org/Talesoft/tale-stream)
[![Coverage](https://img.shields.io/codeclimate/coverage/Talesoft/tale-stream.svg?style=for-the-badge)](https://codeclimate.com/github/Talesoft/tale-stream)

Tale Stream
===========

What is Tale Stream?
--------------------

This is a direct implementation of the PSR-7 `Psr\Http\Message\StreamInterface` and 
the PSR-17 `Psr\Http\Message\StreamFactoryInterface`. It doesn't add any
extra methods, only a few utility stream factories and some useful
iterators for streams.

It acts as a stable streaming library core for full-fledged streaming libraries
like socket implementations or filesystem abstractions.

Installation
------------

```bash
composer req talesoft/tale-stream
```

Usage
-----

Check out the [Functions File](https://github.com/Talesoft/tale-stream/blob/master/src/functions.php) 
to see all things this library does.

### Stream all the things

The heart is the `Tale\Stream` class you can use with the `Tale\stream` 
function. You can pass any resource of type `stream` and have a PSR-7 
compatible stream for any purpose.

```php
use function Tale\stream;

$stream = stream(fopen('/some/file', 'rb+'));

if ($stream->isReadable()) {
    $contents = $stream->read(100);
}

if ($stream->isWritable()) {
    $stream->write('Some Content');
}

$stream->close();
```

### Create a Stream factory for DI containers

```php
use Psr\Http\Message\StreamFactoryInterface;
use Tale\StreamFactory;

$container->add(StreamFactory::class);

//...

$streamFactory = $container->get(StreamFactoryInterface::class);

$stream = $streamFactory->createStreamFromFile('./some-file.txt', 'rb');

echo $stream; //"<contents of some-file.txt>"
```

### File Streams

The filestream works analogous to the 
`fopen($filename, $mode, $useIncludePath, $context)` function.

```php
use function Tale\stream_file;

$stream = stream_file('./some-file.txt', 'rb');

echo $stream->getContents(); //"<contents of some-file.txt>"
```

### Memory Streams

Memory streams only reside in memory and are gone when the execution
ended. This is very useful to create streams on the fly wherever you
need one. They are always readable and writable.

```php
use function Tale\stream_memory;

$stream = stream_memory('Some Content!');

echo $stream->getContents(); //"Some Content!"
```

### Temporary Streams

Temporary streams work like memory streams, but at some point
they will start swapping data into a file to save memory.

```php
use function Tale\stream_memory;

$stream = stream_temp('Some Content!', 1024);
//Stream will start swapping after 1024 bytes
```

#### Input Stream

The input stream is a readable file stream on `php://input`.
In most cases, this is the raw HTTP request body and useful
for APIs that work with structured data formats.

```php
use function Tale\stream_input;

//...

$stream = stream_input();

$data = json_decode($stream->getContents());
echo $data['firstName'];
```

#### Output Stream

The output stream is a writable file stream on `php://output`.
This is mostly the output content that is sent to the browser.

```php
use function Tale\stream_output;

$stream = stream_output();

$stream->write('<h1>This will be sent to the browser</h1>');
```

#### STDIN Stream

The standard input stream is a readable file stream on `php://stdin`.
This is e.g. content from a piped command.

```php
use function Tale\stream_stdin;

$stream = stream_stdin();

echo $stream->getContents(); //"<piped input>"
```

#### STDERR Stream

The standard error stream is a writable file stream on `php://stderr`.
This is mostly used for output of errors in console commands.

```php
use function Tale\stream_stderr;

$stream = stream_stderr();

$stream->write('Error: Something bad happened');
```

#### STDOUT Stream

The standard output stream is a writable file stream on `php://stdout`.
This is mostly console command output.

```php
use function Tale\stream_stdout;

$stream = stream_stdout();

$stream->write("Working...please wait...\n");
```

### Null Stream

The null-stream does nothing. It only implements the interfaces. Useful
to avoid defensive null checks on optional dependencies, to suppress things
and for testing.

```php
use function Tale\stream_null;

$stream = stream_null();

echo $stream->read(100); //Will always return an empty string
```

### Read streams with iterators

The ReadIterator will read a stream chunk-by-chunk 
(default chunk size is 1024). Notice, all the iterators
here work with any PSR-7 stream, not only `Tale\Stream` instances.

```php
use function Tale\stream_memory;
use function Tale\stream_iterator_read;

$stream = stream_memory('abcdefg');

$iterator = stream_iterator_read($stream, 2); //Chunk size of 2

$chunks = iterator_to_array($iterator);
//You could alternatively iterate the iterator with foreach

dump($chunks); //['ab', 'cd', 'ef', 'g']
```

### Iterate lines of a stream

The LineIterator works differently to `fgets()`. While the chunk size
on `fgets()` limits the length a line can has, the LineIterator will
just wait for the actual end of the line and doesn't care about
the chunk size.

```php
use function Tale\stream_memory;
use function Tale\stream_get_lines;

$stream = stream_memory("Line 1\nLine 2\nLine 3");

$lines = stream_get_lines($stream);

dump(iterator_to_array($lines)); //["Line 1", "Line 2", "Line 3"]
```

#### Split streams by delimiters

The SplitIterator is the reason why the LineIterator can work
like it does. It can split streams by any delimiter of any length
and doesn't care about the chunk size of the internal reader.

```php
use function Tale\stream_memory;
use function Tale\stream_split;

$stream = stream_memory('a,b,c,d');

$items = stream_split($stream, ',');

dump(iterator_to_array($items)); //['a', 'b', 'c', 'd']
```

#### Write to streams with iterators

The WriteIterator is a utility to write iterables to streams
easily.

```php
use function Tale\stream_memory;
use function Tale\stream_iterator_write;

function generateLines()
{
    yield "Line 1\n";
    yield "Line 2\n";
    yield "Line 3
}

$stream = stream_memory();

$writer = stream_iterator_write($stream, generateLines());

$writtenBytes = $writer->writeAll();

//You could also iterate the write to leave place for other actions
//e.g. in async environments
```

### Pipe streams

The ReadIterator and WriteIterator combined provide a solid way
to pipe streams efficiently.

```php
use function Tale\stream_memory;
use function Tale\stream_pipe;

$inputStream = stream_memory('Some content');
$outputStream = stream_memory();

$writer = stream_pipe($inputStream, $outputStream);
$writer->writeAll();
```

