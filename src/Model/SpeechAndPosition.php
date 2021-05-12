<?php

namespace Althingi\Model;

class SpeechAndPosition extends Speech
{
    private int $position;

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'position' => $this->position
        ]);
    }
}
