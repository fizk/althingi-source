<?php

namespace Althingi\Model;

use DateTime;

class Plenary implements ModelInterface
{
    private $plenary_id;
    private $assembly_id;
    private ?DateTime $from = null;
    private ?DateTime $to = null;
    private ?string $name = null;

    public function getPlenaryId(): int
    {
        return $this->plenary_id;
    }

    public function setPlenaryId(int $plenary_id): self
    {
        $this->plenary_id = $plenary_id;
        return $this;
    }

    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    public function setAssemblyId(int $assembly_id): self
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    public function getFrom(): ?DateTime
    {
        return $this->from;
    }

    public function setFrom(?DateTime $from = null): self
    {
        $this->from = $from;
        return $this;
    }

    public function getTo(): ?DateTime
    {
        return $this->to;
    }

    public function setTo(?DateTime $to = null): self
    {
        $this->to = $to;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name = null): self
    {
        $this->name = $name;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'plenary_id' => $this->plenary_id,
            'assembly_id' => $this->assembly_id,
            'from' => $this->from?->format('Y-m-d H:i'),
            'to' => $this->to?->format('Y-m-d H:i'),
            'name' => $this->name,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
