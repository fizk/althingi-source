<?php

namespace Althingi\Model;

class Proponent extends Congressman
{
    /** @var  string */
    private $minister;

    /**
     * @return string
     */
    public function getMinister(): ?string
    {
        return $this->minister;
    }

    /**
     * @param string $minister
     * @return Proponent
     */
    public function setMinister(string $minister = null): self
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
