
[![Packagist](https://img.shields.io/packagist/v/talesoft/tale-stream.svg?style=for-the-badge)](https://packagist.org/packages/talesoft/tale-stream)
[![License](https://img.shields.io/github/license/Talesoft/tale-stream.svg?style=for-the-badge)](https://github.com/Talesoft/tale-stream/blob/master/LICENSE.md)
[![CI](https://img.shields.io/travis/Talesoft/tale-stream.svg?style=for-the-badge)](https://travis-ci.org/Talesoft/tale-stream)
[![Coverage](https://img.shields.io/codeclimate/coverage/Talesoft/tale-stream.svg?style=for-the-badge)](https://codeclimate.com/github/Talesoft/tale-stream)

Tale Stream
===========

What is Tale Stream?
--------------------

This is an implementation of the `Psr7\HttpMessage\StreamInterface`. It doesn't add any
extra methods, only a few utility stream classes that extend it.

You can use it as a base for any kind of stream implementation.

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

## Available Streams

- `Tale\Stream\FileStream` -> Same API as `fopen`
- `Tale\Stream\InputStream` -> php://input, rb
- `Tale\Stream\OutputStream` -> php://output, wb
- `Tale\Stream\MemoryStream` -> php://memory, rb+
- `Tale\Stream\TempStream` -> php://temp, rb+
- `Tale\Stream\NullStream` -> Empty readable, writable and seekable stream that implements the interfaces, but does nothing at all
- `Tale\Stream\StandardErrorStream` -> STDERR wrapper
- `Tale\Stream\StandardInputStream` -> STDIN wrapper
- `Tale\Stream\StandardOutputStream` -> STDOUT wrapper
