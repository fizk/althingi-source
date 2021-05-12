<?php

namespace Althingi\Model;

class PresidentPartyProperties implements ModelInterface
{
    private President $president;
    private ?Party $party = null;

    public function getPresident(): President
    {
        return $this->president;
    }

    public function setPresident(President $president): self
    {
        $this->president = $president;
        return $this;
    }

    public function getParty(): ?Party
    {
        return $this->party;
    }

    public function setParty(?Party $party): self
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
