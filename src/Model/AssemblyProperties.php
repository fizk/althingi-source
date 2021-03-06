<?php

namespace Althingi\Model;

class AssemblyProperties implements ModelInterface
{
    /** @var  \Althingi\Model\Assembly */
    private $assembly;

    /** @var  \Althingi\Model\Party[] */
    private $majority = [];

    /** @var  \Althingi\Model\Party[] */
    private $minority = [];

    /** @var  \Althingi\Model\Cabinet */
    private $cabinet;

    /**
     * @return Assembly
     */
    public function getAssembly(): Assembly
    {
        return $this->assembly;
    }

    /**
     * @param Assembly $assembly
     * @return AssemblyProperties
     */
    public function setAssembly(Assembly $assembly): AssemblyProperties
    {
        $this->assembly = $assembly;
        return $this;
    }

    /**
     * @return Party[]
     */
    public function getMajority(): array
    {
        return $this->majority;
    }

    /**
     * @param Party[] $majority
     * @return AssemblyProperties
     */
    public function setMajority(array $majority): AssemblyProperties
    {
        $this->majority = $majority;
        return $this;
    }

    /**
     * @param Party $majority
     * @return AssemblyProperties
     */
    public function addMajority(Party $majority): AssemblyProperties
    {
        $this->majority[] = $majority;
        return $this;
    }

    /**
     * @return int[]
     */
    public function getMajorityPartyIds(): array
    {
        return array_map(function (Party $party) {
            return $party->getPartyId();
        }, $this->getMajority());
    }

    /**
     * @return Party[]
     */
    public function getMinority(): array
    {
        return $this->minority;
    }

    /**
     * @param Party[] $minority
     * @return AssemblyProperties
     */
    public function setMinority(array $minority): AssemblyProperties
    {
        $this->minority = $minority;
        return $this;
    }

    /**
     * @param Party $minority
     * @return AssemblyProperties
     */
    public function addMinority(Party $minority): AssemblyProperties
    {
        $this->minority[] = $minority;
        return $this;
    }

    /**
     * @return Cabinet
     */
    public function getCabinet(): ?Cabinet
    {
        return $this->cabinet;
    }

    /**
     * @param Cabinet $cabinet
     * @return AssemblyProperties
     */
    public function setCabinet(?Cabinet $cabinet): AssemblyProperties
    {
        $this->cabinet = $cabinet;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->assembly->toArray(), [
            'party' => [
                'majority' => $this->majority,
                'minority' => $this->minority
            ],
            'cabinet' => $this->cabinet
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
