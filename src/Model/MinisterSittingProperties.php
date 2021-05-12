<?php

namespace Althingi\Model;

class MinisterSittingProperties implements ModelInterface
{
    private CongressmanPartyProperties $congressman;
    private Ministry $ministry;
    private MinisterSitting $minister_sitting;

    public function getCongressman(): CongressmanPartyProperties
    {
        return $this->congressman;
    }

    public function setCongressman(CongressmanPartyProperties $congressman): self
    {
        $this->congressman = $congressman;
        return $this;
    }

    public function getMinistry(): Ministry
    {
        return $this->ministry;
    }

    public function setMinistry(Ministry $ministry): self
    {
        $this->ministry = $ministry;
        return $this;
    }

    public function getMinisterSitting(): MinisterSitting
    {
        return $this->minister_sitting;
    }

    public function setMinisterSitting(MinisterSitting $minister_sitting): self
    {
        $this->minister_sitting = $minister_sitting;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->minister_sitting->toArray(), [
            'congressman' => $this->congressman,
            'ministry' => $this->ministry,
        ]);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
