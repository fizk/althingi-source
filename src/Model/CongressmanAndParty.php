<?php

namespace Althingi\Model;

class CongressmanAndParty extends Congressman
{
    private ?int $party_id = null;

    public function getPartyId(): ?int
    {
        return $this->party_id;
    }

    public function setPartyId(?int $party_id): self
    {
        $this->party_id = $party_id;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            ['party_id' => $this->party_id]
        );
    }
}
