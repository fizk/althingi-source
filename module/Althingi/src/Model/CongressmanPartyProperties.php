<?php

namespace Althingi\Model;

class CongressmanPartyProperties implements ModelInterface
{
    /** @var  \Althingi\Model\Congressman */
    private $congressman;

    /** @var  \Althingi\Model\Party */
    private $party;

    /** @var \Althingi\Model\Constituency */
    private $constituency = null;

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
    public function setCongressman(Congressman $congressman): CongressmanPartyProperties
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
    public function setParty(Party $party = null): CongressmanPartyProperties
    {
        $this->party = $party;
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
    public function setAssembly(Assembly $assembly): CongressmanPartyProperties
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
    public function setConstituency(?Constituency $constituency): CongressmanPartyProperties
    {
        $this->constituency = $constituency;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->congressman->toArray(), [
            'party' => $this->party,
            'assembly' => $this->assembly,
            'constituency' => $this->constituency
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
