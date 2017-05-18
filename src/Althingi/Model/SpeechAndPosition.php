<?php

namespace Althingi\Model;

class SpeechAndPosition extends Speech
{
    /** @var int */
    private $position;

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return SpeechAndPosition
     */
    public function setPosition(int $position): SpeechAndPosition
    {
        $this->position = $position;
        return $this;
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), ['position' => $this->position]);
    }

}
