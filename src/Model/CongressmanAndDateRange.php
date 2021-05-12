<?php

namespace Althingi\Model;

use DateTime;

class CongressmanAndDateRange extends Congressman
{
    private ?int $time = null;
    private ?DateTime $begin = null;
    private ?DateTime $end = null;

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(?int $time): self
    {
        $this->time = $time;
        return $this;
    }

    public function getBegin(): ?DateTime
    {
        return $this->begin;
    }

    public function setBegin(?DateTime $begin): self
    {
        $this->begin = $begin;
        return $this;
    }

    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    public function setEnd(?DateTime $end): self
    {
        $this->end = $end;
        return $this;
    }

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
