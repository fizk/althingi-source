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
    public function setDate(DateTime $date = null): self
    {
        $this->date = $date;
        return $this;
    }


    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'date' => $this->date?->format('Y-m-d H:i:s'),
        ]);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
