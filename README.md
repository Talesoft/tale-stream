
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

`use Tale\Stream;`

The heart is the `Tale\Stream` class. You can pass any resource
of type `stream` and have a PSR-7 compatible stream for any purpose.

```php
$stream = new Stream(fopen('/some/file', 'rb+'));

if ($stream->isReadable()) {
    $contents = $stream->read(100);
}

if ($stream->isWritable()) {
    $stream->write('Some Content');
}
```

### Utility Streams

#### Stream::createFileStream

The filestream works analogous to the `fopen($filename, $mode, $useIncludePath, $context)` function

```php
$fs = Stream::createFileStream(__DIR__.'/some-file.txt', 'rb');
while (!$fs->eof()) {
    echo $fs->read(32);
}
```

#### Stream::createInputStream

The input stream is a readable FileStream on `php://input`

#### Stream::createOutputStream

The output stream is a writable FileStream on `php://output`

#### Stream::createMemoryStream

The memory stream is a readable and writable FileStream on `php://memory`. Very useful
to provide string-based values to stream-based operations. You can feed it with initial
data:

```php
$ms = Stream::createMemoryStream('I am some content!');

echo $ms->read(10); //"I am some "
echo $ms->read(8); //"content!"
```

#### Stream::createTempStream

The temp stream is a FileStream on `php://temp`. You can set a maximum
memory limit (when the stream starts using a temporary file) and you can 
feed it with initial data, too:

```php
$ts = new TempStream('I am some content!', 1024);

echo $ts->read(10); //"I am some "
echo $ts->read(8); //"content!"
```

#### Stream::createStdinStream

The standard input stream is a readable FileStream on `php://stdin`

#### Stream::createStderrStream

The standard error stream is a writable FileStream on `php://stderr`

#### Stream::createStdoutStream

The standard output stream is a writable FileStream on `php://stdout`

### NullStream

`use Tale\Stream\NullStream`

The null-stream does nothing. It only implements the interfaces. Useful
to avoid defensive null checks on optional dependencies, to suppress things
and for testing.


### Using the factory

`use Tale\Stream\Factory;`

The factory is an implementation of the PSR-17 standard
and provides a way to have a centralized factory for streams
in your dependency injection container.

```php
$factory = new Factory();

$stream = $factory->createStream('some stream content');

$stream = $factory->createStreamFromFile('/some/file', 'rb+');

$stream = $factory->createStreamFromResource(fopen('/some/file', 'rb+'));
```

If you have a Dependency Injection container, you can inject
the factory if you registered it as a service

```php
use Psr\Http\Message\StreamFactoryInterface;

class MyService
{
    private $streamFactory;
    
    public function __construct(StreamFactoryInterface $streamFactory)
    {
        $this->streamFactory = $streamFactory;    
    }
    
    public function doStuff(): void
    {
        $stream = $this->streamFactory->createStreamFromFile(
            __DIR__.'/some-file'
        );
        
        //etc. etc.
    }
}
```

#### Roll your own stream factory

`use Tale\Stream\FactoryTrait;`

If you already have some kind of stream or HTTP factory
or want to roll your own one, there is a trait for you to use
that basically gives you the full functionality of the
default factory.

```php
use Psr\Http\Message\StreamFactoryInterface;

class MyStreamFactory implements StreamFactoryInterface
{
    use FactoryTrait;
    
    //other implementations and stuff
}
```

You can then register it in your DI container and all services
will start using your own implementation.

### Utility iterators

Tale Stream provides some basic iterators to make reading streams
easier by default.


#### ReadIterator

`use Tale\Stream\Iterator\ReadIterator`

ReadIterator will read a stream chunk-by-chunk (default chunk size is 1024)

```php
//Have a stream of some kind
$stream = Stream::createMemoryStream('abcdefg');

//Create a ReadIterator on that stream
$iterator = new ReadIterator($stream, 2); //2 is the chunk size (default: 1024)

//Just iterate the reader to get all chunks
foreach ($iterator as $chunk) {
    var_dump($chunk); //0 => ab, 1 => cd, 2 => ef, 3 => g
}

//Alternatively get all chunks into an array
$chunks = iterator_to_array($iterator);
```

#### SplitIterator

`use Tale\Stream\Iterator\SplitIterator`

SplitIterator will split the content by a delimiter and yield item-by-item.
You can pass any iterator as the first argument.

```php
$stream = Stream::createMemoryStream('ab|cd|ef|g');

$readIterator = new ReadIterator($stream);

$reader = new SplitIterator($readIterator, '|');

foreach ($reader as $item) {
    var_dump($item); //0 => ab, 1 => cd, 2 => ef, 3 => g
}
```

#### LineIterator

`use Tale\Stream\Iterator\LineIterator`

LineIterator will split the stream content by lines and yield line-by-line.
Other than `fgets`, this line reader is not limited to its chunk-size. Lines
can be as long as the PHP process allows and the chunk size does not matter.

```php
$stream = Stream::createMemoryStream("ab\ncd\nde\ng");

$reader = new LineIterator($stream);

foreach ($reader as $item) {
    var_dump($item); //0 => ab, 1 => cd, 2 => ef, 3 => g
}
```

### WriteIterator

`use Tale\Stream\Iterator\WriteIterator`

WriteIterator allows to pipe and filter streams easily and efficiently.

```php
$inputStream = Stream::createMemoryStream('stream content');

$outputStream = Stream::createMemoryStream();

$chunkSize = 2048;

$readIterator = new ReadIterator($inputStream, $chunkSize);

$iterator = new WriteIterator($outputStream, $readIterator);

//Write the whole stream at once
var_dump($iterator->writeAll()); //int(14)

//Write the stream sequentially, chunk-by-chunk
foreach ($iterator as $writtenBytes) {
    echo "Wrote {$writtenBytes} bytes";
}
```

The WriteIterator can take any iterable as its source, so you can
also pipe things like generators or even arrays

```php
function generateContents(): Generator
{
    yield "Line 1\n";
    yield "Line 2\n";
    yield "Line 3\n";
}

$stream = Stream::createFileStream('./some-file.txt', 'wb');

$iterator = new WriteIterator($stream, generateContents());
$iterator->writeAll();

//./some-file.txt now contains:
//    Line 1\n
//    Line 2\n
//    Line 3\n
```

Using iterators you can filter streams during piping in many different ways

```php
use CallbackFilterIterator;
use Tale\Iterator\SuffixIterator; //requires talesoft/tale-iterator
use Tale\Stream\MemoryStream;
use Tale\Stream\Iterator\ReadIterator;
use Tale\Stream\Iterator\WriteIterator;
use Tale\Stream\Iterator\LineIterator;

$inputStream = Stream::createMemoryStream("ab\ncd\nde\ng");

$outputStream = Stream::createMemoryStream();

//Use a LineIterator to cleanly read lines
$reader = new LineIterator($inputStream);

//Will filter all lines that match "de"
$filteredReader = new CallbackFilterIterator($reader, function (string $line) {
    return $line !== 'de';
});

//Will add "\n" to all lines
$lfSuffixer = new SuffixIterator($filteredReader, "\n");

$writer = new WriteIterator($outputStream, $lfSuffixer);
$writer->writeAll();

var_dump((string)$outputStream); //"ab\ncd\ng"
```

