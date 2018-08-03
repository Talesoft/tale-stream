<?php
declare(strict_types=1);

namespace Tale\Test\Stream\Iterator;

use PHPUnit\Framework\TestCase;

abstract class AbstractIteratorTest extends TestCase
{
    public function assertIterator(iterable $iterator, array $values): void
    {
        $loops = 0;
        foreach ($iterator as $i => $item) {

            $this->assertArrayHasKey($i, $values);
            $this->assertEquals($values[$i], $item);
            $loops++;
        }
        $this->assertEquals(\count($values), $loops);
    }
}