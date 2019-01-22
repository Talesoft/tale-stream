<?php declare(strict_types=1);

namespace Tale\Test\Stream\Iterator;

use PHPUnit\Framework\TestCase;

abstract class AbstractIteratorTest extends TestCase
{
    public static function assertIterator(array $expected, iterable $iterator): void
    {
        $loops = 0;
        foreach ($iterator as $i => $item) {
            self::assertArrayHasKey($i, $expected);
            self::assertSame($expected[$i], $item);
            $loops++;
        }
        self::assertSame(\count($expected), $loops);
    }
}
