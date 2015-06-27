<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 8/06/15
 * Time: 9:10 PM
 */

namespace Althingi\Controller;

use Zend\Stdlib\RequestInterface;

trait Range
{
    private $perPage = 25;

    /**
     * Split up Range HTTP header or return default.
     *
     * @param RequestInterface $request
     * @param int $count
     * @return object
     * @todo if range is something like 'items hundur-vei'
     */
    private function getRange(RequestInterface $request, $count = 0)
    {
        /** @var $range \Zend\Http\Header\Range */
        if ($range = $request->getHeader('Range')) {
            $match = [];
            preg_match('/([0-9]*)-([0-9]*)/', $range->getFieldValue(), $match);
            if (count($match) == 3) {
                $from = (int) $match[1];
                $to = (int) $match[2];

                //NEGATIVE RANGE
                if ($to - $from < 0) {
                    return [
                        'from' => 0,
                        'to' => 0
                    ];
                    //OUT OF RANGE
                } elseif ($to > $count) {
                    //BOTH OUT OF RANGE
                    if ($from > $count) {
                        return [
                            'from' => 0,
                            'to' => 0
                        ];
                    }
                    //LOWER BOUND IN RANGE
                    return [
                        'from' => $from,
                        'to' => $count
                    ];
                    //RANGE BIGGER
                } elseif ($to - $from > $this->perPage) {
                    return [
                        'from' => $from,
                        'to' => $from + $this->perPage
                    ];
                }

                return [
                    'from' => $from,
                    'to' => $to
                ];
            }
        }

        return [
            'from' => 0,
            'to' => ($this->perPage > $count) ? $count : $this->perPage
        ];
    }
}
