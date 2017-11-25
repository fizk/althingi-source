<?php

namespace Althingi\Model;

class CabinetProperties implements ModelInterface
{
    /** @var  \Althingi\Model\Cabinet */
    private $cabinet;

    /** @var  \Althingi\Model\CongressmanPartyProperties[] */
    private $congressmen;

    /**
     * @return Cabinet
     */
    public function getCabinet(): Cabinet
    {
        return $this->cabinet;
    }

    /**
     * @param Cabinet $cabinet
     * @return CabinetProperties
     */
    public function setCabinet(Cabinet $cabinet): CabinetProperties
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
     * @return CabinetProperties
     */
    public function setCongressmen(array $congressmen): CabinetProperties
    {
        $this->congressmen = $congressmen;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->cabinet->toArray(), [
            'congressmen' => $this->congressmen,
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
