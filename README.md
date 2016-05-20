
# Tale Stream
**A Tale Framework Component**

# What is Tale Stream?

Tale Stream is a little utility that eases up the usage of basic PHP streams.

It is PSR-7 compliant

# Installation

Install via Composer

```bash
composer require "talesoft/tale-stream:*"
composer install
```

# Usage

```php

$stream = new Stream('/some/file');

if ($stream->isReadable())
    $contents = $stream->read(100);

if ($stream->isWritable())
    $stream->write('Some Content');
    
```

# Available Streams

- `Tale\Stream\InputStream` -> php://input, rb
- `Tale\Stream\OutputStream` -> php://output, wb
- `Tale\Stream\MemoryStream` -> php://memory, wb+
- `Tale\Stream\TempStream` -> php://temp, wb+
- `Tale\Stream\StringStream` -> MemoryStream with initial content via constructor