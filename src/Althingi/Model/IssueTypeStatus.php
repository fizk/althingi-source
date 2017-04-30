<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
class IssueTypeStatus implements ModelInterface
{
    /** @var  int */
    private $count;

    /** @var  string */
    private $status;

    /**
     * @return int
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return IssueTypeStatus
     */
    public function setCount(int $count = null): IssueTypeStatus
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return IssueTypeStatus
     */
    public function setStatus(string $status = null): IssueTypeStatus
    {
        $this->status = $status;
        return $this;
    }

    public function toArray()
    {
        return [
            'count' => $this->count,
            'status' => $this->status,
        ];
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
