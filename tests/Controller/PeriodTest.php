<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 10/04/2016
 * Time: 1:14 PM
 */

namespace Althingi\Controller;

class PeriodTest extends \PHPUnit_Framework_TestCase
{
    public function testTrue()
    {
        $range = [
            (object)[
                'count' => "2",
                'vote_date' => "2015-09-14"
            ]
        ];





        $begin = new \DateTime('2015-09-10');
        $end = new \DateTime('2015-09-20');
        $end = $end->modify('+1 day');

        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($begin, $interval, $end);

        $collection = array_map(function ($dateObject) use ($range) {
            $date = $dateObject->format('Y-m-d');
            $count = array_filter($range, function ($item) use ($date) {
                return $item->vote_date == $date;
            });
            return count($count) == 1
                ? $count[0]
                : (object) [
                    'count' => 0,
                    'vote_date' => $date
                ];

        }, iterator_to_array($daterange));

        print_r($collection);


    }
}
