<?php

namespace Althingi\Model;

class Inflation implements ModelInterface
{
    /** @var  int */
    private $id;

    /** @var  float */
    private $value;

    /** @var  \DateTime */
    private $date;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Inflation
     */
    public function setId(int $id): Inflation
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param float $value
     * @return Inflation
     */
    public function setValue(float $value): Inflation
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Inflation
     */
    public function setDate(?\DateTime $date): Inflation
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
            'id' => $this->id,
            'value' => $this->value,
            'date' => $this->date ? $this->date->format('Y-m-d') : null,
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
