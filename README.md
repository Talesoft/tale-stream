
[![Packagist](https://img.shields.io/packagist/v/talesoft/tale-stream.svg?style=for-the-badge)](https://packagist.org/packages/talesoft/tale-stream)
[![License](https://img.shields.io/github/license/Talesoft/tale-stream.svg?style=for-the-badge)](https://github.com/Talesoft/tale-stream/blob/master/LICENSE.md)
[![CI](https://img.shields.io/travis/Talesoft/tale-stream.svg?style=for-the-badge)](https://travis-ci.org/Talesoft/tale-stream)
[![Coverage](https://img.shields.io/codeclimate/coverage/Talesoft/tale-stream.svg?style=for-the-badge)](https://codeclimate.com/github/Talesoft/tale-stream)

Tale Stream
===========

What is Tale Stream?
--------------------

This is an implementation of the `Psr\HttpMessage\StreamInterface`. It doesn't add any
extra methods, only a few utility stream classes that extend it.

You can use it as a base for any kind of full-fledged stream implementation.

Installation
------------

```bash
composer require talesoft/tale-stream
```

Usage
-----

```php
use Tale\Stream;

$stream = new Stream(fopen('/some/file', 'rb+'));

if ($stream->isReadable()) {
    $contents = $stream->read(100);
}

if ($stream->isWritable()) {
    $stream->write('Some Content');
}
    
```

### Using the factory

```php
use Tale\Stream\Factory;

$factory = new Factory();

$stream = $factory->createStream('some stream content');

$stream = $factory->createStreamFromFile('/some/file', 'rb+');

$stream = $factory->createStreamFromResource(fopen('/some/file', 'rb+'));
```

### Using the iterators

ReadIterator will read a stream chunk-by-chunk (default chunk size is 1024)

```php
use Tale\Stream\MemoryStream;
use Tale\Stream\Iterator\ReadIterator;

$stream = new MemoryStream('abcdefg');

$reader = new ReadIterator($stream, 2);

foreach ($reader as $chunk) {
    var_dump($chunk); //0 => ab, 1 => cd, 2 => ef, 3 => g
}
```

SplitIterator will split the content by a delimiter and yield item-by-item

```php
use Tale\Stream\MemoryStream;
use Tale\Stream\Iterator\ReadIterator;
use Tale\Stream\Iterator\SplitIterator;

$stream = new MemoryStream('ab|cd|ef|g');

$reader = new SplitIterator($stream, '|');

foreach ($reader as $item) {
    var_dump($item); //0 => ab, 1 => cd, 2 => ef, 3 => g
}
```

LineIterator will split the stream content by lines and yield line-by-line

```php

use Tale\Stream\MemoryStream;
use Tale\Stream\Iterator\ReadIterator;
use Tale\Stream\Iterator\LineIterator;

$stream = new MemoryStream("ab\ncd\nde\ng");

$reader = new LineIterator($stream);

foreach ($reader as $item) {
    var_dump($item); //0 => ab, 1 => cd, 2 => ef, 3 => g
}
```

### Piping streams with iterators

WriteIterator allows to pipe and filter streams easily and efficiently.

```php
use Tale\Stream\MemoryStream;
use Tale\Stream\Iterator\ReadIterator;
use Tale\Stream\Iterator\WriteIterator;

$inputStream = new MemoryStream();
$outputStream = new MemoryStream();
$chunkSize = 2048;

$pipe = new WriteIterator($outputStream, new ReadIterator($inputStream, $chunkSize));
foreach ($pipe as $writtenBytes) { //The actual piping process, chunk-by-chunk
    echo "Wrote {$writtenBytes} bytes";
}

//alternatively you can use iterator_to_array to pipe the whole stream at once
$writtenBytesArray = iterator_to_array($pipe);
```

Using iterators you can filter streams during piping in many different ways

```php
use CallbackFilterIterator;
use Tale\Iterator\SuffixIterator; //requires talesoft/tale-iterator
use Tale\Stream\MemoryStream;
use Tale\Stream\Iterator\ReadIterator;
use Tale\Stream\Iterator\WriteIterator;
use Tale\Stream\Iterator\LineIterator;

$inputStream = new MemoryStream("ab\ncd\nde\ng");

//Use a LineIterator to cleanly read lines
$reader = new LineIterator($inputStream);

//Will filter all lines that match "de"
$filteredReader = new CallbackFilterIterator($reader, function (string $line) {
    return $line !== 'de';
});

//Will add "\n" to all lines
$lfSuffixer = new SuffixIterator($filteredReader, "\n");

$outputStream = new MemoryStream();

$pipe = new WriteIterator($outputStream, $lfSuffixer);
$writtenBytes = iterator_to_array($pipe); //The actual piping process, chunk-by-chunk

var_dump((string)$outputStream); //"ab\ncd\ng"
```



### Available Streams

- `Tale\Stream\FileStream` -> Same API as `fopen`
- `Tale\Stream\InputStream` -> php://input, rb
- `Tale\Stream\OutputStream` -> php://output, wb
- `Tale\Stream\MemoryStream` -> php://memory, rb+
- `Tale\Stream\TempStream` -> php://temp, rb+
- `Tale\Stream\NullStream` -> Empty readable, writable and seekable stream that implements the interfaces, but does nothing at all
- `Tale\Stream\StandardErrorStream` -> STDERR wrapper
- `Tale\Stream\StandardInputStream` -> STDIN wrapper
- `Tale\Stream\StandardOutputStream` -> STDOUT wrapper
