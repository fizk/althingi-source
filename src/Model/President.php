<?php

namespace Althingi\Model;

use DateTime;

class President implements ModelInterface
{
    private ?int $president_id = null;
    private int $congressman_id;
    private int $assembly_id;
    private string $title;
    private DateTime $from;
    private ? DateTime $to = null;
    private ?string $abbr = null;

    public function getPresidentId(): ?int
    {
        return $this->president_id;
    }

    public function setPresidentId(?int $president_id): self
    {
        $this->president_id = $president_id;
        return $this;
    }

    public function getCongressmanId(): int
    {
        return $this->congressman_id;
    }

    public function setCongressmanId(int $congressman_id): self
    {
        $this->congressman_id = $congressman_id;
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getAbbr(): ?string
    {
        return $this->abbr;
    }

    public function setAbbr(?string $abbr): self
    {
        $this->abbr = $abbr;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'president_id' => $this->president_id,
            'congressman_id' => $this->congressman_id,
            'assembly_id' => $this->assembly_id,
            'from' => $this->from?->format('Y-m-d'),
            'to' => $this->to?->format('Y-m-d'),
            'title' => $this->title,
            'abbr' => $this->abbr,
        ];
    }
}
