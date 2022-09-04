<?php

namespace Althingi\Model;

use DateTime;

class Election implements ModelInterface
{
    private $election_id;
    private $date;
    private ?string $title = null;
    private ?string $description = null;

    public function getElectionId(): int
    {
        return $this->election_id;
    }

    public function setElectionId(int $election_id): self
    {
        $this->election_id = $election_id;
        return $this;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'election_id' => $this->election_id,
            'date' => $this->date?->format('Y-m-d'),
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
