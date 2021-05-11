<?php

namespace Althingi\Model;

class AssemblyStatusProperties implements ModelInterface
{
    /** @var  \Althingi\Model\DateAndCount[] */
    private $votes = [];

    /** @var  \Althingi\Model\DateAndCount[] */
    private $speeches = [];

    /** @var  \Althingi\Model\PartyAndTime[] */
    private $party_times = [];

    /** @var  \Althingi\Model\Election */
    private $election;

    /** @var  \Althingi\Model\PartyAndElection[] */
    private $election_results;

    /** @var float */
    private $averageAge = 0;

    /**
     * @return DateAndCount[]
     */
    public function getVotes(): array
    {
        return $this->votes;
    }

    /**
     * @param DateAndCount[] $votes
     * @return AssemblyStatusProperties
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
     * @return AssemblyStatusProperties
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
     * @return AssemblyStatusProperties
     */
    public function setPartyTimes(array $partyTimes): self
    {
        $this->party_times = $partyTimes;
        return $this;
    }

    /**
     * @return Election
     */
    public function getElection(): ?Election
    {
        return $this->election;
    }

    /**
     * @param Election $election
     * @return AssemblyStatusProperties
     */
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
     * @return AssemblyStatusProperties
     */
    public function setElectionResults(array $electionResults): self
    {
        $this->election_results = $electionResults;
        return $this;
    }

    /**
     * @return float
     */
    public function getAverageAge(): float
    {
        return $this->averageAge;
    }

    /**
     * @param float $averageAge
     * @return AssemblyStatusProperties
     */
    public function setAverageAge(float $averageAge): self
    {
        $this->averageAge = $averageAge;
        return $this;
    }

    /**
     * @return array
     */
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

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
