<?php

namespace Althingi\Model;

class SpeechCongressmanProperties implements ModelInterface
{
    /** @var  \Althingi\Model\CongressmanPartyProperties */
    private $congressman;

    /** @var  \Althingi\Model\Speech */
    private $speech;

    /**
     * @return CongressmanPartyProperties
     */
    public function getCongressman(): CongressmanPartyProperties
    {
        return $this->congressman;
    }

    /**
     * @param CongressmanPartyProperties $congressman
     * @return SpeechCongressmanProperties
     */
    public function setCongressman(CongressmanPartyProperties $congressman): self
    {
        $this->congressman = $congressman;
        return $this;
    }

    /**
     * @return Speech
     */
    public function getSpeech(): Speech
    {
        return $this->speech;
    }

    /**
     * @param Speech $speech
     * @return SpeechCongressmanProperties
     */
    public function setSpeech(Speech $speech): self
    {
        $this->speech = $speech;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
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
