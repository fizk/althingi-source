<?php

namespace Althingi\Model;

class MinisterSittingProperties implements ModelInterface
{

    /** @var \Althingi\Model\CongressmanPartyProperties */
    private $congressman;

    /** @var \Althingi\Model\Ministry */
    private $ministry;

    /** @var \Althingi\Model\MinisterSitting */
    private $minister_sitting;

    /**
     * @return CongressmanPartyProperties
     */
    public function getCongressman(): CongressmanPartyProperties
    {
        return $this->congressman;
    }

    /**
     * @param CongressmanPartyProperties $congressman
     * @return MinisterSittingProperties
     */
    public function setCongressman(CongressmanPartyProperties $congressman): MinisterSittingProperties
    {
        $this->congressman = $congressman;
        return $this;
    }

    /**
     * @return Ministry
     */
    public function getMinistry(): Ministry
    {
        return $this->ministry;
    }

    /**
     * @param Ministry $ministry
     * @return MinisterSittingProperties
     */
    public function setMinistry(Ministry $ministry): MinisterSittingProperties
    {
        $this->ministry = $ministry;
        return $this;
    }

    /**
     * @return MinisterSitting
     */
    public function getMinisterSitting(): MinisterSitting
    {
        return $this->minister_sitting;
    }

    /**
     * @param MinisterSitting $minister_sitting
     * @return MinisterSittingProperties
     */
    public function setMinisterSitting(MinisterSitting $minister_sitting): MinisterSittingProperties
    {
        $this->minister_sitting = $minister_sitting;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->minister_sitting->toArray(), [
            'congressman' => $this->congressman,
            'ministry' => $this->ministry,
        ]);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
