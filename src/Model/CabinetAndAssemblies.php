<?php

namespace Althingi\Model;

class CabinetAndAssemblies implements ModelInterface
{
    private Cabinet $cabinet;
    /** @var \Althingi\Model\Assembly[] */
    private array $assemblies;

    public function getCabinet(): Cabinet
    {
        return $this->cabinet;
    }

    public function setCabinet(Cabinet $cabinet): static
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
     */
    public function setAssemblies(array $assemblies): static
    {
        $this->assemblies = $assemblies;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->cabinet->toArray(), [
            'assemblies' => $this->assemblies,
        ]);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
