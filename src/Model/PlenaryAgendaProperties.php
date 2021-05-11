<?php
namespace Althingi\Model;

class PlenaryAgendaProperties implements ModelInterface
{

    /** @var \Althingi\Model\PlenaryAgenda */
    private $plenaryAgenda;

    /** @var \Althingi\Model\Issue */
    private $issue;

    /** @var \Althingi\Model\CongressmanPartyProperties */
    private $posedCongressman = null;

    /** @var \Althingi\Model\CongressmanPartyProperties */
    private $answererCongressman = null;

    /** @var \Althingi\Model\CongressmanPartyProperties */
    private $counterAnswererCongressman = null;

    /** @var \Althingi\Model\CongressmanPartyProperties */
    private $instigatorCongressman = null;

    /**
     * @return PlenaryAgenda
     */
    public function getPlenaryAgenda(): PlenaryAgenda
    {
        return $this->plenaryAgenda;
    }

    /**
     * @param PlenaryAgenda $plenaryAgenda
     * @return PlenaryAgendaProperties
     */
    public function setPlenaryAgenda(PlenaryAgenda $plenaryAgenda): self
    {
        $this->plenaryAgenda = $plenaryAgenda;
        return $this;
    }

    /**
     * @return Issue
     */
    public function getIssue(): Issue
    {
        return $this->issue;
    }

    /**
     * @param Issue $issue
     * @return PlenaryAgendaProperties
     */
    public function setIssue(Issue $issue): self
    {
        $this->issue = $issue;
        return $this;
    }

    /**
     * @return CongressmanPartyProperties
     */
    public function getPosedCongressman(): CongressmanPartyProperties
    {
        return $this->posedCongressman;
    }

    /**
     * @param CongressmanPartyProperties $posedCongressman
     * @return PlenaryAgendaProperties
     */
    public function setPosedCongressman(CongressmanPartyProperties $posedCongressman): self
    {
        $this->posedCongressman = $posedCongressman;
        return $this;
    }

    /**
     * @return CongressmanPartyProperties
     */
    public function getAnswererCongressman(): CongressmanPartyProperties
    {
        return $this->answererCongressman;
    }

    /**
     * @param CongressmanPartyProperties $answererCongressman
     * @return PlenaryAgendaProperties
     */
    public function setAnswererCongressman(CongressmanPartyProperties $answererCongressman): self
    {
        $this->answererCongressman = $answererCongressman;
        return $this;
    }

    /**
     * @return CongressmanPartyProperties
     */
    public function getCounterAnswererCongressman(): CongressmanPartyProperties
    {
        return $this->counterAnswererCongressman;
    }

    /**
     * @param CongressmanPartyProperties $counterAnswererCongressman
     * @return PlenaryAgendaProperties
     */
    public function setCounterAnswererCongressman(
        CongressmanPartyProperties $counterAnswererCongressman
    ): self {
        $this->counterAnswererCongressman = $counterAnswererCongressman;
        return $this;
    }

    /**
     * @return CongressmanPartyProperties
     */
    public function getInstigatorCongressman(): CongressmanPartyProperties
    {
        return $this->instigatorCongressman;
    }

    /**
     * @param CongressmanPartyProperties $instigatorCongressman
     * @return PlenaryAgendaProperties
     */
    public function setInstigatorCongressman(CongressmanPartyProperties $instigatorCongressman): self
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

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
