<?php

namespace Althingi\Model;

use DateTime;

class CongressmanAndCabinet extends Congressman
{
    private string $title;
    private ?DateTime $date = null;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'title' => $this->title,
                'date' => $this->date ? $this->date->format('Y-m-d') : null,
            ]
        );
    }
}
