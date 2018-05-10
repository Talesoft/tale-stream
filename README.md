
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

$stream = new Stream('/some/file');

if ($stream->isReadable()) {
    $contents = $stream->read(100);
}

if ($stream->isWritable()) {
    $stream->write('Some Content');
}
    
```

# Available Streams

- `Tale\Stream\InputStream` -> php://input, rb
- `Tale\Stream\OutputStream` -> php://output, wb
- `Tale\Stream\MemoryStream` -> php://memory, wb+
- `Tale\Stream\TempStream` -> php://temp, wb+
- `Tale\Stream\StringStream` -> MemoryStream with initial content via constructor