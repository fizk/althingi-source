<?php

namespace Althingi\Model;

class ValueAndCount implements ModelInterface
{
    /** @var int */
    private $count = 0;

    /** @var string */
    private $value;

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return ValueAndCount
     */
    public function setCount(int $count): ValueAndCount
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return ValueAndCount
     */
    public function setValue(?string $value): ValueAndCount
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'value' => $this->value,
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
