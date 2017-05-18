<?php

namespace Althingi\Model;

class IssueAndDate extends Issue
{
    /** @var \DateTime */
    private $date;

    /**
     * @return \DateTime
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return IssueAndDate
     */
    public function setDate(\DateTime $date = null): IssueAndDate
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

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
