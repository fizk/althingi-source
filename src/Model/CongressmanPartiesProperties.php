<?php

namespace Althingi\Model;

class CongressmanPartiesProperties implements ModelInterface
{
    private Congressman $congressman;
    /** @var  \Althingi\Model\Party[] */
    private array $parties = [];
    private ?Constituency $constituency = null;
    private Assembly $assembly;

    public function getCongressman(): Congressman
    {
        return $this->congressman;
    }

    public function setCongressman(Congressman $congressman): self
    {
        $this->congressman = $congressman;
        return $this;
    }

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
     * @return Constituency
     */
    public function getConstituency(): ?Constituency
    {
        return $this->constituency;
    }

    public function setConstituency(?Constituency $constituency): self
    {
        $this->constituency = $constituency;
        return $this;
    }

    /**
     * @return Party[]
     */
    public function getParties(): array
    {
        return $this->parties;
    }

    /**
     * @param Party[] $parties
     */
    public function setParties(array $parties): self
    {
        $this->parties = $parties;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->congressman->toArray(), [
            'parties' => $this->parties,
            'assembly' => $this->assembly,
            'constituency' => $this->constituency
        ]);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
