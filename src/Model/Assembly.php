<?php

namespace Althingi\Model;

use DateTime;

class Assembly implements ModelInterface
{
    private int $assembly_id;
    private DateTime $from;
    private ?DateTime $to = null;

    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    public function setAssemblyId(int $assembly_id): self
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    public function getFrom(): DateTime
    {
        return $this->from;
    }

    public function setFrom(DateTime $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function getTo(): ?DateTime
    {
        return $this->to;
    }

    public function setTo(?DateTime $to): self
    {
        $this->to = $to;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'assembly_id' => $this->assembly_id,
            'from' => $this->from?->format('Y-m-d'),
            'to' => $this->to?->format('Y-m-d'),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
