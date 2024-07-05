<?php

namespace Althingi\Model;

class MinisterSessionProperties implements ModelInterface
{
    private CongressmanPartyProperties $congressman;
    private Ministry $ministry;
    private MinisterSession $minister_session;

    public function getCongressman(): CongressmanPartyProperties
    {
        return $this->congressman;
    }

    public function setCongressman(CongressmanPartyProperties $congressman): static
    {
        $this->congressman = $congressman;
        return $this;
    }

    public function getMinistry(): Ministry
    {
        return $this->ministry;
    }

    public function setMinistry(Ministry $ministry): static
    {
        $this->ministry = $ministry;
        return $this;
    }

    public function getMinisterSession(): MinisterSession
    {
        return $this->minister_session;
    }

    public function setMinisterSession(MinisterSession $minister_session): static
    {
        $this->minister_session = $minister_session;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->minister_session->toArray(), [
            'congressman' => $this->congressman,
            'ministry' => $this->ministry,
        ]);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
