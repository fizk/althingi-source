<?php

namespace Althingi\Model;

class AssemblyProperties implements ModelInterface
{
    private Assembly $assembly;
    /** @var \Althingi\Model\Party[] */
    private array $majority = [];
    /** @var \Althingi\Model\Party[] */
    private array $minority = [];
    private Cabinet $cabinet;

    public function getAssembly(): Assembly
    {
        return $this->assembly;
    }

    public function setAssembly(Assembly $assembly): self
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
     */
    public function setMajority(array $majority): self
    {
        $this->majority = $majority;
        return $this;
    }

    public function addMajority(Party $majority): self
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
     */
    public function setMinority(array $minority): self
    {
        $this->minority = $minority;
        return $this;
    }

    public function addMinority(Party $minority): self
    {
        $this->minority[] = $minority;
        return $this;
    }

    public function getCabinet(): ?Cabinet
    {
        return $this->cabinet;
    }

    public function setCabinet(?Cabinet $cabinet): self
    {
        $this->cabinet = $cabinet;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->assembly->toArray(), [
            'party' => [
                'majority' => $this->majority,
                'minority' => $this->minority
            ],
            'cabinet' => $this->cabinet
        ]);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
