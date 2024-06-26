<?php

namespace Althingi\Model;

class Constituency implements ModelInterface
{
    private int $constituency_id;
    private ?string $name = null;
    private ?string $abbr_short = null;
    private ?string $abbr_long = null;
    private ?string $description = null;

    public function getConstituencyId(): int
    {
        return $this->constituency_id;
    }

    public function setConstituencyId(int $constituency_id): static
    {
        $this->constituency_id = $constituency_id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'constituency_id' => $this->constituency_id,
            'name' => $this->name,
            'abbr_short' => $this->abbr_short,
            'abbr_long' => $this->abbr_long,
            'description' => $this->description,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
