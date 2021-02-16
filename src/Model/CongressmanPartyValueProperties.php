<?php

namespace Althingi\Model;

class CongressmanPartyValueProperties extends CongressmanPartyProperties
{
    /** @var int | null  */
    private $value = null;

    /**
     * @return int|null
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    /**
     * @param int|null $value
     * @return CongressmanPartyValueProperties
     */
    public function setValue(?int $value): CongressmanPartyValueProperties
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'value' => $this->value,
        ]);
    }
}
