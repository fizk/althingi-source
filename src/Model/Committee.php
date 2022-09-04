<?php

namespace Althingi\Model;

class Committee implements ModelInterface
{
    private int $committee_id;
    private ?string $name = null;
    private int $first_assembly_id;
    private ?int $last_assembly_id = null;
    private ?string $abbr_long = null;
    private ?string $abbr_short = null;

    public function getCommitteeId(): int
    {
        return $this->committee_id;
    }

    public function setCommitteeId(int $committee_id): self
    {
        $this->committee_id = $committee_id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getFirstAssemblyId(): int
    {
        return $this->first_assembly_id;
    }

    public function setFirstAssemblyId(int $first_assembly_id): self
    {
        $this->first_assembly_id = $first_assembly_id;
        return $this;
    }

    public function getLastAssemblyId(): ?int
    {
        return $this->last_assembly_id;
    }

    public function setLastAssemblyId(?int $last_assembly_id): self
    {
        $this->last_assembly_id = $last_assembly_id;
        return $this;
    }

    public function getAbbrLong(): ?string
    {
        return $this->abbr_long;
    }

    public function setAbbrLong(?string $abbr_long): self
    {
        $this->abbr_long = $abbr_long;
        return $this;
    }

    public function getAbbrShort(): ?string
    {
        return $this->abbr_short;
    }

    public function setAbbrShort(?string $abbr_short): self
    {
        $this->abbr_short = $abbr_short;
        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'committee_id' => $this->committee_id,
            'name' => $this->name,
            'first_assembly_id' => $this->first_assembly_id,
            'last_assembly_id' => $this->last_assembly_id,
            'abbr_long' => $this->abbr_long,
            'abbr_short' => $this->abbr_short,
        ];
    }
}
