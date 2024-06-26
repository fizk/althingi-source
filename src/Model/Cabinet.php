<?php

namespace Althingi\Model;

use DateTime;

class Cabinet implements ModelInterface
{
    private int $cabinet_id;
    private ?string $title = null;
    private ?DateTime $from = null;
    private ?DateTime $to = null;
    private ?string $description = null;

    public function getCabinetId(): int
    {
        return $this->cabinet_id;
    }

    public function setCabinetId(int $cabinet_id): static
    {
        $this->cabinet_id = $cabinet_id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getFrom(): ?\DateTime
    {
        return $this->from;
    }

    public function setFrom(?\DateTime $from): static
    {
        $this->from = $from;
        return $this;
    }

    public function getTo(): ?\DateTime
    {
        return $this->to;
    }

    public function setTo(?\DateTime $to): static
    {
        $this->to = $to;
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
            'cabinet_id' => $this->cabinet_id,
            'title' => $this->title,
            'description' => $this->description,
            'from' => $this->from?->format('Y-m-d'),
            'to' => $this->to?->format('Y-m-d'),
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
