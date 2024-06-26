<?php

namespace Althingi\Model;

class Proponent extends Congressman
{
    private ?string $minister = null;

    public function getMinister(): ?string
    {
        return $this->minister;
    }

    public function setMinister(?string $minister): static
    {
        $this->minister = $minister;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            ['minister' => $this->minister]
        );
    }
}
