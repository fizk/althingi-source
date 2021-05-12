<?php

namespace Althingi\Model;

class CongressmanPartyProperties implements ModelInterface
{
    private Congressman $congressman;
    private ?Party $party = null;
    /** @var  \Althingi\Model\Party[] */
    private array $parties = [];
    private ?Constituency $constituency = null;
    /** @var \Althingi\Model\Ministry[]  */
    private array $ministries = [];
    /** @var \Althingi\Model\MinisterSitting[]  */
    private array $ministrySittings = [];
    private ?Assembly $assembly = null;

    public function getCongressman(): Congressman
    {
        return $this->congressman;
    }

    public function setCongressman(Congressman $congressman): self
    {
        $this->congressman = $congressman;
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

    public function getAssembly(): ?Assembly
    {
        return $this->assembly;
    }

    public function setAssembly(?Assembly $assembly): self
    {
        $this->assembly = $assembly;
        return $this;
    }

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
     * @return Ministry[]
     */
    public function getMinistries(): array
    {
        return $this->ministries;
    }

    /**
     * @param Ministry[] $ministries
     */
    public function setMinistries(array $ministries): self
    {
        $this->ministries = $ministries;
        return $this;
    }

    /**
     * @return MinisterSitting[]
     */
    public function getMinistrySittings(): array
    {
        return $this->ministrySittings;
    }

    /**
     * @param MinisterSitting[] $ministrySittings
     */
    public function setMinistrySittings(array $ministrySittings): self
    {
        $this->ministrySittings = $ministrySittings;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->congressman->toArray(), [
            'party' => $this->party,
            'parties' => $this->parties,
            'assembly' => $this->assembly,
            'constituency' => $this->constituency,
            'ministries' => $this->ministries,
            'ministrySittings' => $this->ministrySittings
        ]);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
