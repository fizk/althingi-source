<?php

namespace Althingi\Model;

class PartyAndElection extends Party
{
    /** @var  float */
    private $results;

    /** @var  int */
    private $seat;

    /** @var  int */
    private $election_id;

    /** @var  int */
    private $election_result_id;

    /** @var  int */
    private $assembly_id;

    /**
     * @return float
     */
    public function getResults(): ?float
    {
        return $this->results;
    }

    /**
     * @param float $results
     * @return PartyAndElection
     */
    public function setResults(float $results): PartyAndElection
    {
        $this->results = $results;
        return $this;
    }

    /**
     * @return int
     */
    public function getSeat(): ?int
    {
        return $this->seat;
    }

    /**
     * @param int $seat
     * @return PartyAndElection
     */
    public function setSeat(?int $seat): PartyAndElection
    {
        $this->seat = $seat;
        return $this;
    }

    /**
     * @return int
     */
    public function getElectionId(): int
    {
        return $this->election_id;
    }

    /**
     * @param int $election_id
     * @return PartyAndElection
     */
    public function setElectionId(int $election_id): PartyAndElection
    {
        $this->election_id = $election_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getElectionResultId(): int
    {
        return $this->election_result_id;
    }

    /**
     * @param int $election_result_id
     * @return PartyAndElection
     */
    public function setElectionResultId(int $election_result_id): PartyAndElection
    {
        $this->election_result_id = $election_result_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    /**
     * @param int $assembly_id
     * @return PartyAndElection
     */
    public function setAssemblyId(int $assembly_id): PartyAndElection
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            [
                'election_id' => $this->election_id,
                'election_result_id' => $this->election_result_id,
                'assembly_id' => $this->assembly_id,
                'seat' => $this->seat,
                'results' => $this->results
            ]
        );
    }
}
