<?php

namespace Althingi\Model;

class SpeechCongressmanProperties implements ModelInterface
{
    private CongressmanPartyProperties $congressman;
    private Speech $speech;

    public function getCongressman(): CongressmanPartyProperties
    {
        return $this->congressman;
    }

    public function setCongressman(CongressmanPartyProperties $congressman): self
    {
        $this->congressman = $congressman;
        return $this;
    }

    public function getSpeech(): Speech
    {
        return $this->speech;
    }

    public function setSpeech(Speech $speech): self
    {
        $this->speech = $speech;
        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge($this->speech->toArray(), [
            'congressman' => $this->congressman->toArray()
        ]);
    }
}
