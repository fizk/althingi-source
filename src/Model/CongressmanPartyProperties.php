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
    /** @var \Althingi\Model\MinisterSession[]  */
    private array $ministrySittings = [];
    private ?Assembly $assembly = null;

    public function getCongressman(): Congressman
    {
        return $this->congressman;
    }

    public function setCongressman(Congressman $congressman): static
    {
        $this->congressman = $congressman;
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
    public function setParties(array $parties): static
    {
        $this->parties = $parties;
        return $this;
    }

    public function getAssembly(): ?Assembly
    {
        return $this->assembly;
    }

    public function setAssembly(?Assembly $assembly): static
    {
        $this->assembly = $assembly;
        return $this;
    }

    public function getConstituency(): ?Constituency
    {
        return $this->constituency;
    }

    public function setConstituency(?Constituency $constituency): static
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
    public function setMinistries(array $ministries): static
    {
        $this->ministries = $ministries;
        return $this;
    }

    /**
     * @return MinisterSession[]
     */
    public function getMinistrySittings(): array
    {
        return $this->ministrySittings;
    }

    /**
     * @param MinisterSession[] $ministrySittings
     */
    public function setMinistrySittings(array $ministrySittings): static
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

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
