<?php

namespace AlthingiTest\Lib;

use Althingi\Utils\DateAndCountSequence;
use Althingi\Model\DateAndCount;
use PHPUnit\Framework\TestCase;

class DateAndCountSequenceTest extends TestCase
{
    public function testWithEmptyRange()
    {
        $begin = new \DateTime('2000-01-01');
        $end = new \DateTime('2000-01-05');

        $expectedResult = [
            (new DateAndCount())->setDate(new \DateTime('2000-01-01'))->setCount(0),
            (new DateAndCount())->setDate(new \DateTime('2000-01-02'))->setCount(0),
            (new DateAndCount())->setDate(new \DateTime('2000-01-03'))->setCount(0),
            (new DateAndCount())->setDate(new \DateTime('2000-01-04'))->setCount(0),
            (new DateAndCount())->setDate(new \DateTime('2000-01-05'))->setCount(0),
        ];
        $actualResult = DateAndCountSequence::buildDateRange($begin, $end);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testWithRange()
    {
        $begin = new \DateTime('2000-01-01');
        $end = new \DateTime('2000-01-05');

        $range = [
            (new DateAndCount())->setDate(new \DateTime('2000-01-02'))->setCount(2),
        ];

        $expectedResult = [
            (new DateAndCount())->setDate(new \DateTime('2000-01-01'))->setCount(0),
            (new DateAndCount())->setDate(new \DateTime('2000-01-02'))->setCount(2),
            (new DateAndCount())->setDate(new \DateTime('2000-01-03'))->setCount(0),
            (new DateAndCount())->setDate(new \DateTime('2000-01-04'))->setCount(0),
            (new DateAndCount())->setDate(new \DateTime('2000-01-05'))->setCount(0),
        ];
        $actualResult = DateAndCountSequence::buildDateRange($begin, $end, $range);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testWithManyDates()
    {
        $begin = new \DateTime('2000-01-01');
        $end = new \DateTime('2000-01-05');

        $range = [
            (new DateAndCount())->setDate(new \DateTime('2000-01-02'))->setCount(2),
            (new DateAndCount())->setDate(new \DateTime('2000-01-04'))->setCount(4),
            (new DateAndCount())->setDate(new \DateTime('2000-01-04'))->setCount(4),
        ];

        $expectedResult = [
            (new DateAndCount())->setDate(new \DateTime('2000-01-01'))->setCount(0),
            (new DateAndCount())->setDate(new \DateTime('2000-01-02'))->setCount(2),
            (new DateAndCount())->setDate(new \DateTime('2000-01-03'))->setCount(0),
            (new DateAndCount())->setDate(new \DateTime('2000-01-04'))->setCount(8),
            (new DateAndCount())->setDate(new \DateTime('2000-01-05'))->setCount(0),
        ];
        $actualResult = DateAndCountSequence::buildDateRange($begin, $end, $range);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testWithMonthRange()
    {
        $begin = new \DateTime('2000-01-01');
        $end = new \DateTime('2000-02-01');

        $range = [
            (new DateAndCount())->setDate(new \DateTime('2000-01-01'))->setCount(2),
        ];

        $expectedResult = [
            (new DateAndCount())->setDate(new \DateTime('2000-01-01'))->setCount(2),
            (new DateAndCount())->setDate(new \DateTime('2000-02-01'))->setCount(0),
        ];
        $actualResult = DateAndCountSequence::buildDateRange($begin, $end, $range, 'P1M');

        $this->assertEquals($expectedResult, $actualResult);
    }
}
