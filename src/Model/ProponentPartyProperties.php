<?php

namespace Althingi\Model;

class ProponentPartyProperties extends CongressmanPartyProperties
{
    /** @var int */
    private $order = 0;

    /** @var string | null  */
    private $minister = null;

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return ProponentPartyProperties
     */
    public function setOrder(int $order): ProponentPartyProperties
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getMinister(): ?string
    {
        return $this->minister;
    }

    /**
     * @param null|string $minister
     * @return ProponentPartyProperties
     */
    public function setMinister(?string $minister): ProponentPartyProperties
    {
        $this->minister = $minister;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'order' => $this->order,
            'minister' => $this->minister,
        ]);
    }
}
