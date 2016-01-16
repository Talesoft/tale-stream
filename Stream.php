<?php

namespace Tale;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;

class Stream implements StreamInterface
{

    const DEFAULT_MODE = 'rb+';

    private $_context;
    private $_mode;
    private $_metadata;

    public function __construct($context, $mode = null)
    {

        $this->_context = $context;
        $this->_mode = $mode ? $mode : self::DEFAULT_MODE;

        if ($this->_context instanceof UriInterface)
            $this->_context = (string)$this->_context;

        if (is_string($this->_context))
            $this->_context = fopen($this->_context, $this->_mode);

        if (!is_resource($this->_context))
            throw new InvalidArgumentException(
                "Argument 1 needs to be resource or path/URI"
            );

        $this->_metadata = stream_get_meta_data($this->_context);
    }

    public function __destruct()
    {

        $this->close();
    }


    public function getContext()
    {

        return $this->_context;
    }

    public function getMode()
    {

        return $this->_mode;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {

        if (!$this->_context) {

            return;
        }

        $context = $this->detach();
        fclose($context);
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {

        $context = $this->_context;
        $this->_context = null;
        $this->_metadata = null;

        return $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {

        if ($this->_context === null)
            return null;

        $stat = fstat($this->_context);

        return $stat['size'];
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {

        $result = ftell($this->_context);
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {

        if (!$this->_context)
            return true;

        return feof($this->_context);
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable()
    {

        if (!$this->_context)
            return false;

        return $this->getMetadata('seekable') ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = \SEEK_SET)
    {

        if (!$this->isSeekable())
            throw new RuntimeException(
                "Stream is not seekable"
            );

        fseek($this->_context, $offset, $whence);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {

        return $this->seek(0);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {

        if (!$this->_context)
            return false;

        $mode = $this->getMetadata('mode');
        return (strstr($mode, 'w') || strstr($mode, 'x') || strstr($mode, 'c') || strstr($mode, '+'));
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {

        if (!$this->isWritable())
            throw new RuntimeException(
                "Stream is not writable"
            );

        return fwrite($this->_context, $string);
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {

        if (!$this->_context)
            return false;

        $mode = $this->getMetadata('mode');
        return (strstr($mode, 'r') || strstr($mode, '+'));
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {

        if (!$this->isReadable())
            throw new RuntimeException(
                "Stream is not readable"
            );

        return fread($this->_context, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {

        if (!$this->isReadable())
            throw new RuntimeException(
                "Stream is not readable"
            );

        return stream_get_contents($this->_context);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {

        if ($key === null)
            return $this->_metadata;

        if (!isset($this->_metadata[$key]))
            return null;

        return $this->_metadata[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {

        if (!$this->isReadable()) {

            return '';
        }

        if ($this->isSeekable())
            $this->rewind();

        return $this->getContents();
    }

    private function __clone() {}
}