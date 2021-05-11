<?php

namespace Althingi\Model;

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
    public function setPresident(President $president): self
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
    public function setParty(Party $party = null): self
    {
        $this->party = $party;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->president->toArray(), [
            'party' => $this->party
        ]);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
