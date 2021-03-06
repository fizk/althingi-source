<?php

namespace Althingi\Model;

class CabinetAndAssemblies implements ModelInterface
{
    /** @var  \Althingi\Model\Cabinet */
    private $cabinet;

    /** @var  \Althingi\Model\Assembly[] */
    private $assemblies;

    /**
     * @return Cabinet
     */
    public function getCabinet(): Cabinet
    {
        return $this->cabinet;
    }

    /**
     * @param Cabinet $cabinet
     * @return CabinetAndAssemblies
     */
    public function setCabinet(Cabinet $cabinet): CabinetAndAssemblies
    {
        $this->cabinet = $cabinet;
        return $this;
    }

    /**
     * @return Assembly[]
     */
    public function getAssemblies(): array
    {
        return $this->assemblies;
    }

    /**
     * @param Assembly[] $assemblies
     * @return CabinetAndAssemblies
     */
    public function setAssemblies(array $assemblies): CabinetAndAssemblies
    {
        $this->assemblies = $assemblies;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->cabinet->toArray(), [
            'assemblies' => $this->assemblies,
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
