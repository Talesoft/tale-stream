<?php

namespace Tale\Test\Stream;

use Tale\Stream\StringStream;

class StringStreamTest extends \PHPUnit_Framework_TestCase
{

    public function testReadingAndWriting()
    {

        $stream = new StringStream();
        $str = 'This is some content';
        $stream->write($str);

        $this->assertEquals(strlen($str), $stream->getSize());

    }
}