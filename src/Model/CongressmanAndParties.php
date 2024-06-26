<?php

namespace Althingi\Model;

class CongressmanAndParties implements ModelInterface
{
    private Congressman $congressman;
    /** @var  \Althingi\Model\Party[] */
    private array $parties = [];

    public function getCongressman(): Congressman
    {
        return $this->congressman;
    }

    public function setCongressman(Congressman $congressman): static
    {
        $this->congressman = $congressman;
        return $this;
    }

    /**
     * @return \Althingi\Model\Party[]
     */
    public function getParties(): array
    {
        return $this->parties;
    }

    /**
     * @param \Althingi\Model\Party[] $parties
     */
    public function setParties(array $parties = []): static
    {
        $this->parties = $parties;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->congressman->toArray(),
            ['parties' => $this->getParties()]
        );
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
