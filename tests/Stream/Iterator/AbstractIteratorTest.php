<?php
declare(strict_types=1);

namespace Tale\Test\Stream\Iterator;

use PHPUnit\Framework\TestCase;

abstract class AbstractIteratorTest extends TestCase
{
    public function assertIterator(iterable $iterator, array $values): void
    {
        var_dump("assertIterator(".json_encode($values).")");
        $loops = 0;
        foreach ($iterator as $i => $item) {
            var_dump("assertIterator: {$i} => {$item}");
            $this->assertArrayHasKey($i, $values);
            $this->assertEquals($values[$i], $item);
            $loops++;
        }
        $this->assertEquals(\count($values), $loops);
    }
}
