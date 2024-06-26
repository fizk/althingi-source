<?php

namespace Althingi\Model;

class CabinetProperties implements ModelInterface
{
    private Cabinet $cabinet;
    /** @var \Althingi\Model\CongressmanPartyProperties[] */
    private array $congressmen = [];

    public function getCabinet(): Cabinet
    {
        return $this->cabinet;
    }

    public function setCabinet(Cabinet $cabinet): static
    {
        $this->cabinet = $cabinet;
        return $this;
    }

    /**
     * @return CongressmanPartyProperties[]
     */
    public function getCongressmen(): array
    {
        return $this->congressmen;
    }

    /**
     * @param CongressmanPartyProperties[] $congressmen
     */
    public function setCongressmen(array $congressmen): static
    {
        $this->congressmen = $congressmen;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->cabinet->toArray(), [
            'congressmen' => $this->congressmen,
        ]);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
