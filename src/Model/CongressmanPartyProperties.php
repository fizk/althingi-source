<?php

namespace Althingi\Model;

class CongressmanPartyProperties implements ModelInterface
{
    /** @var  \Althingi\Model\Congressman */
    private $congressman;

    /** @var  \Althingi\Model\Party */
    private $party;

    /** @var  \Althingi\Model\Party[] */
    private $parties = [];

    /** @var \Althingi\Model\Constituency */
    private $constituency = null;

    /** @var \Althingi\Model\Ministry[]  */
    private $ministries = [];

    /** @var \Althingi\Model\MinisterSitting[]  */
    private $ministrySittings = [];

    /** @var  \Althingi\Model\Assembly */
    private $assembly;

    /**
     * @return Congressman
     */
    public function getCongressman(): Congressman
    {
        return $this->congressman;
    }

    /**
     * @param Congressman $congressman
     * @return CongressmanPartyProperties
     */
    public function setCongressman(Congressman $congressman): self
    {
        $this->congressman = $congressman;
        return $this;
    }

    /**
     * @return Party|null
     */
    public function getParty(): ?Party
    {
        return $this->party;
    }

    /**
     * @param Party $party
     * @return CongressmanPartyProperties|null
     */
    public function setParty(Party $party = null): self
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
     * @return CongressmanPartyProperties
     */
    public function setParties(array $parties): self
    {
        $this->parties = $parties;
        return $this;
    }

    /**
     * @return Assembly
     */
    public function getAssembly(): Assembly
    {
        return $this->assembly;
    }

    /**
     * @param Assembly $assembly
     * @return CongressmanPartyProperties
     */
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

    /**
     * @param Constituency $constituency
     * @return CongressmanPartyProperties
     */
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
     * @return CongressmanPartyProperties
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
     * @return CongressmanPartyProperties
     */
    public function setMinistrySittings(array $ministrySittings): self
    {
        $this->ministrySittings = $ministrySittings;
        return $this;
    }

    /**
     * @return array
     */
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

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
