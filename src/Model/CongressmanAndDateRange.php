<?php

namespace Althingi\Model;

use DateTime;

class CongressmanAndDateRange extends Congressman
{
    /** @var  int */
    private $time;

    /** @var  \DateTime */
    private $begin;

    /** @var  \DateTime */
    private $end;

    /**
     * @return int
     */
    public function getTime(): ?int
    {
        return $this->time;
    }

    /**
     * @param int $time
     * @return CongressmanAndDateRange
     */
    public function setTime(int $time = null): self
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBegin(): ?DateTime
    {
        return $this->begin;
    }

    /**
     * @param \DateTime $begin
     * @return CongressmanAndDateRange
     */
    public function setBegin(DateTime $begin = null): self
    {
        $this->begin = $begin;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     * @return CongressmanAndDateRange
     */
    public function setEnd(DateTime $end = null): self
    {
        $this->end = $end;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'time' => $this->time,
                'begin' => $this->begin ? $this->begin->format('Y-m-d') : null,
                'end' => $this->end ? $this->end->format('Y-m-d') : null,
            ]
        );
    }
}
