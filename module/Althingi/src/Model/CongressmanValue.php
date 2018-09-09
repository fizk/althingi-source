<?php

namespace Althingi\Model;

class CongressmanValue extends Congressman
{
    /** @var  int */
    private $value;

    /**
     * @return int
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    /**
     * @param int $value
     * @return CongressmanValue
     */
    public function setValue(?int $value): CongressmanValue
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            ['value' => $this->value]
        );
    }
}
