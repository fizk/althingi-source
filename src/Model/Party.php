<?php

namespace Althingi\Model;

class Party implements ModelInterface
{
    private int $party_id;
    private string $name;
    private ?string $abbr_short = null;
    private ?string $abbr_long = null;
    private ?string $color = null;

    public function getPartyId(): ?int
    {
        return $this->party_id;
    }

    public function setPartyId(?int $party_id): static
    {
        $this->party_id = $party_id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getAbbrShort(): ?string
    {
        return $this->abbr_short;
    }

    public function setAbbrShort(?string $abbr_short): static
    {
        $this->abbr_short = $abbr_short;
        return $this;
    }

    public function getAbbrLong(): ?string
    {
        return $this->abbr_long;
    }

    public function setAbbrLong(?string $abbr_long): static
    {
        $this->abbr_long = $abbr_long;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'party_id' => $this->party_id,
            'name' => $this->name,
            'abbr_short' => $this->abbr_short,
            'abbr_long' => $this->abbr_long,
            'color' => $this->color,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
