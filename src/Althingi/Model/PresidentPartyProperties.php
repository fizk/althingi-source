<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
class PresidentPartyProperties implements ModelInterface
{
    /** @var  \Althingi\Model\President */
    private $president;

    /** @var  \Althingi\Model\Party */
    private $party;

    /**
     * @return President
     */
    public function getPresident(): President
    {
        return $this->president;
    }

    /**
     * @param President $president
     * @return PresidentPartyProperties
     */
    public function setPresident(President $president): PresidentPartyProperties
    {
        $this->president = $president;
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
     * @return PresidentPartyProperties|null
     */
    public function setParty(Party $party = null): PresidentPartyProperties
    {
        $this->party = $party;
        return $this;
    }

    public function toArray()
    {
        return array_merge($this->president->toArray(), [
            'party' => $this->party
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
