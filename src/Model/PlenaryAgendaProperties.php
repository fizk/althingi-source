<?php

namespace Althingi\Model;

class PlenaryAgendaProperties implements ModelInterface
{
    private PlenaryAgenda $plenaryAgenda;
    private Issue $issue;
    private ?CongressmanPartyProperties $posedCongressman = null;
    private ?CongressmanPartyProperties $answererCongressman = null;
    private ?CongressmanPartyProperties $counterAnswererCongressman = null;
    private ?CongressmanPartyProperties $instigatorCongressman = null;

    public function getPlenaryAgenda(): PlenaryAgenda
    {
        return $this->plenaryAgenda;
    }

    public function setPlenaryAgenda(PlenaryAgenda $plenaryAgenda): static
    {
        $this->plenaryAgenda = $plenaryAgenda;
        return $this;
    }

    public function getIssue(): Issue
    {
        return $this->issue;
    }

    public function setIssue(Issue $issue): static
    {
        $this->issue = $issue;
        return $this;
    }

    public function getPosedCongressman(): ?CongressmanPartyProperties
    {
        return $this->posedCongressman;
    }

    public function setPosedCongressman(?CongressmanPartyProperties $posedCongressman): static
    {
        $this->posedCongressman = $posedCongressman;
        return $this;
    }

    public function getAnswererCongressman(): ?CongressmanPartyProperties
    {
        return $this->answererCongressman;
    }

    public function setAnswererCongressman(?CongressmanPartyProperties $answererCongressman): static
    {
        $this->answererCongressman = $answererCongressman;
        return $this;
    }

    public function getCounterAnswererCongressman(): ?CongressmanPartyProperties
    {
        return $this->counterAnswererCongressman;
    }

    public function setCounterAnswererCongressman(
        ?CongressmanPartyProperties $counterAnswererCongressman
    ): static {
        $this->counterAnswererCongressman = $counterAnswererCongressman;
        return $this;
    }

    public function getInstigatorCongressman(): ?CongressmanPartyProperties
    {
        return $this->instigatorCongressman;
    }

    public function setInstigatorCongressman(?CongressmanPartyProperties $instigatorCongressman): static
    {
        $this->instigatorCongressman = $instigatorCongressman;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->plenaryAgenda->toArray(), [
            'issue' => $this->issue,
            'posed_congressman' => $this->posedCongressman,
            'answerer_congressman' => $this->answererCongressman,
            'counter_answerer_congressman' => $this->counterAnswererCongressman,
            'instigator_congressman' => $this->instigatorCongressman,
        ]);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
