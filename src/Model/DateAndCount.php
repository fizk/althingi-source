<?php

namespace Althingi\Model;

use DateTime;

class DateAndCount implements ModelInterface
{
    /** @var int */
    private $count = 0;

    /** @var \DateTime */
    private $date;

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return DateAndCount
     */
    public function setCount(int $count): DateAndCount
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return DateAndCount
     */
    public function setDate(DateTime $date = null): DateAndCount
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'date' => $this->date ? $this->date->format('c') : null,
            'count' => $this->count
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
