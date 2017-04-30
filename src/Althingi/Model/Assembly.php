<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
class Assembly implements ModelInterface
{
    /** @var  int */
    private $assembly_id;

    /** @var  \DateTime */
    private $from;

    /** @var  \DateTime */
    private $to;

    /**
     * @return int
     */
    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    /**
     * @param int $assembly_id
     * @return $this
     */
    public function setAssemblyId(int $assembly_id)
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFrom(): \DateTime
    {
        return $this->from;
    }

    /**
     * @param \DateTime $from
     * @return $this
     */
    public function setFrom(\DateTime $from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTo(): ?\DateTime
    {
        return $this->to;
    }

    /**
     * @param \DateTime $to
     * @return $this
     */
    public function setTo(\DateTime $to = null)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'assembly_id' => $this->assembly_id,
            'from' => $this->from ? $this->from->format('Y-m-d') : null,
            'to' => $this->to ? $this->to->format('Y-m-d') : null,
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
