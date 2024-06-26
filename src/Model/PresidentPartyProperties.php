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

    public function setPresident(President $president): static
    {
        $this->president = $president;
        return $this;
    }

    public function getParty(): ?Party
    {
        return $this->party;
    }

    public function setParty(?Party $party): static
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

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
