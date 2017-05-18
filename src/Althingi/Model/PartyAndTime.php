<?php

namespace Althingi\Model;

class PartyAndTime extends Party
{
    private $total_time = 0;

    /**
     * @return mixed
     */
    public function getTotalTime(): int
    {
        return $this->total_time;
    }

    /**
     * @param mixed $total_time
     * @return PartyAndTime
     */
    public function setTotalTime(int $total_time = 0): PartyAndTime
    {
        $this->total_time = $total_time;
        return $this;
    }


    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            ['total_time' => $this->total_time]
        );
    }
}
