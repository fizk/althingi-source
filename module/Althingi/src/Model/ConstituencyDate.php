<?php
namespace Althingi\Model;

use DateTime;

class ConstituencyDate extends Constituency
{
    /** @var DateTime */
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
     * @return ConstituencyDate
     */
    public function setDate(?DateTime $date): ConstituencyDate
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'date' => $this->date ? $this->date->format('Y-m-d') : null,
        ]);
    }
}
