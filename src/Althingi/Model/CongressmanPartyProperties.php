<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
class CongressmanPartyProperties implements ModelInterface
{
    /** @var  \Althingi\Model\Congressman */
    private $congressman;

    /** @var  \Althingi\Model\Party */
    private $party;

    /**
     * @return Congressman
     */
    public function getCongressman(): Congressman
    {
        return $this->congressman;
    }

    /**
     * @param Congressman $congressman
     * @return CongressmanPartyProperties
     */
    public function setCongressman(Congressman $congressman): CongressmanPartyProperties
    {
        $this->congressman = $congressman;
        return $this;
    }

    /**
     * @return Party|null
     */
    public function getParty(): ?Party
    {
        return $this->party;
    }

    /**
     * @param Party $party
     * @return CongressmanPartyProperties|null
     */
    public function setParty(Party $party = null): CongressmanPartyProperties
    {
        $this->party = $party;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->congressman->toArray(), [
            'party' => $this->party
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
