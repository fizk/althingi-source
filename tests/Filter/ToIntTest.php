<?php

namespace Althingi\Filter;

use PHPUnit\Framework\TestCase;

class ToIntTest extends TestCase
{
    /**
     * @dataProvider provider
     */
    public function testTrue($in, $out)
    {

        $this->assertEquals((new ToInt)->filter($in), $out);
    }

    public function provider()
    {
        return [
            [1, 1],
            [0, 0],
            ['1', 1],
            ['0', 0],
            ['-1', -1],
            ['1234', 1234],
            [1234, 1234],
            [12.34, 12],
            [12.94, 12],
            ['a', null],
        ];
    }
}
