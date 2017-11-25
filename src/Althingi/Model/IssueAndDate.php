<?php

namespace Althingi\Model;

use DateTime;

class IssueAndDate extends Issue
{
    /** @var \DateTime */
    private $date;

    /**
     * @return \DateTime
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return IssueAndDate
     */
    public function setDate(DateTime $date = null): IssueAndDate
    {
        $this->date = $date;
        return $this;
    }


    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'date' => $this->date ? $this->date->format('Y-m-d H:i:s') : null,
        ]);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
