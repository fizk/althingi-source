<?php

namespace Althingi\Model;

use DateTime;

class PresidentCongressman extends Congressman
{
    private int $president_id;
    private int $assembly_id;
    private DateTime $from;
    private ?DateTime $to = null;
    private string $title;
    private ?string $abbr = null;

    public function getPresidentId(): int
    {
        return $this->president_id;
    }

    public function setPresidentId(int $president_id): static
    {
        $this->president_id = $president_id;
        return $this;
    }

    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    public function setAssemblyId(int $assembly_id): static
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    public function getFrom(): DateTime
    {
        return $this->from;
    }

    public function setFrom(DateTime $from): static
    {
        $this->from = $from;
        return $this;
    }

    public function getTo(): ?DateTime
    {
        return $this->to;
    }

    public function setTo(?DateTime $to): static
    {
        $this->to = $to;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getAbbr(): ?string
    {
        return $this->abbr;
    }

    public function setAbbr(?string $abbr): static
    {
        $this->abbr = $abbr;
        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'president_id' => $this->president_id,
            'assembly_id' => $this->assembly_id,
            'from' => $this->from?->format('Y-m-d'),
            'to' => $this->to?->format('Y-m-d'),
            'title' => $this->title,
            'abbr' => $this->abbr,
        ]);
    }
}
