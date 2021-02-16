<?php

namespace Althingi\Model;

class CongressmanPartiesProperties implements ModelInterface
{
    /** @var  \Althingi\Model\Congressman */
    private $congressman;

    /** @var  \Althingi\Model\Party[] */
    private $parties = [];

    /** @var \Althingi\Model\Constituency */
    private $constituency = null;

    /** @var  \Althingi\Model\Assembly */
    private $assembly;

    /**
     * @return \Althingi\Model\Congressman
     */
    public function getCongressman(): Congressman
    {
        return $this->congressman;
    }

    /**
     * @param Congressman $congressman
     * @return CongressmanPartiesProperties
     */
    public function setCongressman(Congressman $congressman): CongressmanPartiesProperties
    {
        $this->congressman = $congressman;
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
     * @return CongressmanPartiesProperties
     */
    public function setAssembly(Assembly $assembly): CongressmanPartiesProperties
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
     * @return CongressmanPartiesProperties
     */
    public function setConstituency(?Constituency $constituency): CongressmanPartiesProperties
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
     * @return CongressmanPartiesProperties
     */
    public function setParties(array $parties): CongressmanPartiesProperties
    {
        $this->parties = $parties;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->congressman->toArray(), [
            'parties' => $this->parties,
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
