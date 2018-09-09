<?php
/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 10/14/17
 * Time: 9:51 AM
 */

namespace Althingi\Lib;

use Althingi\Model\DateAndCount;

class DateAndCountSequence
{
    /**
     * @param \DateTime $begin
     * @param \DateTime|null $end
     * @param \Althingi\Model\DateAndCount[]
     * @param string $interval
     * @return \Althingi\Model\DateAndCount[]
     */
    public static function buildDateRange(
        \DateTime $begin,
        \DateTime $end = null,
        $range = [],
        string $interval = 'P1D'
    ): array {
        $end = $end ? : new \DateTime();
        $end = $end->modify('+1 day');
        $interval = new \DateInterval($interval);
        $dateRange = new \DatePeriod($begin, $interval, $end);

        return array_map(function ($item) use ($range) {
            $rangeItems = array_filter($range, function (DateAndCount $rangeDate) use ($item) {
                return $rangeDate->getDate() == $item;
            });
            $totalCount = array_reduce($rangeItems, function (int $carry, DateAndCount $item) {
                return $carry + $item->getCount();
            }, 0);

            return (new DateAndCount())->setDate($item)->setCount($totalCount);
        }, iterator_to_array($dateRange));
    }
}
