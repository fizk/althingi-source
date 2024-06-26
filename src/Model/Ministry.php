<?php

namespace Althingi\Model;

class Ministry implements ModelInterface
{
    private $ministry_id;
    private ?string $name = null;
    private ?string $abbr_short = null;
    private ?string $abbr_long = null;
    private ?int $first = null;
    private ?int $last = null;

    public function getMinistryId(): int
    {
        return $this->ministry_id;
    }

    public function setMinistryId(int $ministry_id): static
    {
        $this->ministry_id = $ministry_id;
        return $this;
    }

    public function getName(): string
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

    public function getFirst(): ?int
    {
        return $this->first;
    }

    public function setFirst(?int $first): static
    {
        $this->first = $first;
        return $this;
    }

    public function getLast(): ?int
    {
        return $this->last;
    }

    public function setLast(?int $last): static
    {
        $this->last = $last;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'ministry_id' => $this->ministry_id,
            'name' => $this->name,
            'abbr_short' => $this->abbr_short,
            'abbr_long' => $this->abbr_long,
            'first' => $this->first,
            'last' => $this->last,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
