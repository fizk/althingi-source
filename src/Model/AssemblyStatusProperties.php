<?php

namespace Althingi\Model;

class AssemblyStatusProperties implements ModelInterface
{
    /** @var \Althingi\Model\DateAndCount[] */
    private array $votes = [];
    /** @var \Althingi\Model\DateAndCount[] */
    private array $speeches = [];
    /** @var \Althingi\Model\PartyAndTime[] */
    private array $party_times = [];
    private ?Election $election = null;
    /** @var \Althingi\Model\PartyAndElection[] */
    private array $election_results;
    private float $averageAge = 0;

    /**
     * @return DateAndCount[]
     */
    public function getVotes(): array
    {
        return $this->votes;
    }

    /**
     * @param DateAndCount[] $votes
     */
    public function setVotes(array $votes): self
    {
        $this->votes = $votes;
        return $this;
    }

    /**
     * @return DateAndCount[]
     */
    public function getSpeeches(): array
    {
        return $this->speeches;
    }

    /**
     * @param DateAndCount[] $speeches
     */
    public function setSpeeches(array $speeches): self
    {
        $this->speeches = $speeches;
        return $this;
    }

    /**
     * @return PartyAndTime[]
     */
    public function getPartyTimes(): array
    {
        return $this->party_times;
    }

    /**
     * @param PartyAndTime[] $partyTimes
     */
    public function setPartyTimes(array $partyTimes): self
    {
        $this->party_times = $partyTimes;
        return $this;
    }

    public function getElection(): ?Election
    {
        return $this->election;
    }

    public function setElection(?Election $election): self
    {
        $this->election = $election;
        return $this;
    }

    /**
     * @return PartyAndElection[]
     */
    public function getElectionResults(): array
    {
        return $this->election_results;
    }

    /**
     * @param PartyAndElection[] $electionResults
     */
    public function setElectionResults(array $electionResults): self
    {
        $this->election_results = $electionResults;
        return $this;
    }

    public function getAverageAge(): float
    {
        return $this->averageAge;
    }

    public function setAverageAge(float $averageAge = 0): self
    {
        $this->averageAge = $averageAge;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'votes' => $this->votes,
            'speeches' => $this->speeches,
            'party_times' => $this->party_times,
            'election' => $this->election,
            'election_results' => $this->election_results,
            'average_age' => $this->averageAge,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
