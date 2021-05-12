<?php

namespace Althingi\Model;

class ProponentPartyProperties extends CongressmanPartyProperties
{
    private int $order = 0;
    private ?string $minister = null;

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getMinister(): ?string
    {
        return $this->minister;
    }

    public function setMinister(?string $minister): self
    {
        $this->minister = $minister;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'order' => $this->order,
            'minister' => $this->minister,
        ]);
    }
}
