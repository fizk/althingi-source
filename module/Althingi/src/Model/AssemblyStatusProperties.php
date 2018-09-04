<?php

namespace Althingi\Model;

class AssemblyStatusProperties implements ModelInterface
{
    /** @var  \Althingi\Model\IssueTypeStatus[] */
    private $bills;

    /** @var  \Althingi\Model\IssueTypeStatus[] */
    private $government_bills;

    /** @var  \Althingi\Model\AssemblyStatus[] */
    private $types;

    /** @var  \Althingi\Model\DateAndCount[] */
    private $votes;

    /** @var  \Althingi\Model\DateAndCount[] */
    private $speeches;

    /** @var  \Althingi\Model\PartyAndTime[] */
    private $party_times;

    /** @var  \Althingi\Model\CategoryAndCount[] */
    private $categories;

    /** @var  \Althingi\Model\Election */
    private $election;

    /** @var  \Althingi\Model\PartyAndElection[] */
    private $election_results;

    /**
     * @return IssueTypeStatus[]
     */
    public function getBills(): array
    {
        return $this->bills;
    }

    /**
     * @param IssueTypeStatus[] $bills
     * @return AssemblyStatusProperties
     */
    public function setBills(array $bills): AssemblyStatusProperties
    {
        $this->bills = $bills;
        return $this;
    }

    /**
     * @return IssueTypeStatus[]
     */
    public function getGovernmentBills(): array
    {
        return $this->government_bills;
    }

    /**
     * @param IssueTypeStatus[] $governmentBills
     * @return AssemblyStatusProperties
     */
    public function setGovernmentBills(array $governmentBills): AssemblyStatusProperties
    {
        $this->government_bills = $governmentBills;
        return $this;
    }

    /**
     * @return AssemblyStatus[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param AssemblyStatus[] $types
     * @return AssemblyStatusProperties
     */
    public function setTypes(array $types): AssemblyStatusProperties
    {
        $this->types = $types;
        return $this;
    }

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
    public function setVotes(array $votes): AssemblyStatusProperties
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
    public function setSpeeches(array $speeches): AssemblyStatusProperties
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
    public function setPartyTimes(array $partyTimes): AssemblyStatusProperties
    {
        $this->party_times = $partyTimes;
        return $this;
    }

    /**
     * @return CategoryAndCount[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param CategoryAndCount[] $categories
     * @return AssemblyStatusProperties
     */
    public function setCategories(array $categories): AssemblyStatusProperties
    {
        $this->categories = $categories;
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
    public function setElection(Election $election = null): AssemblyStatusProperties
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
    public function setElectionResults(array $electionResults): AssemblyStatusProperties
    {
        $this->election_results = $electionResults;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'bills' => $this->bills,
            'government_bills' => $this->government_bills,
            'types' => $this->types,
            'votes' => $this->votes,
            'speeches' => $this->speeches,
            'party_times' => $this->party_times,
            'categories' => $this->categories,
            'election' => $this->election,
            'election_results' => $this->election_results
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
