<?php

namespace Althingi\Model;

class PartyAndElection extends Party
{
    private ?float $results = null;
    private ?int $seat = null;
    private int $election_id;
    private int $election_result_id;
    private int $assembly_id;

    public function getResults(): ?float
    {
        return $this->results;
    }

    public function setResults(?float $results): static
    {
        $this->results = $results;
        return $this;
    }

    public function getSeat(): ?int
    {
        return $this->seat;
    }

    public function setSeat(?int $seat): static
    {
        $this->seat = $seat;
        return $this;
    }

    public function getElectionId(): int
    {
        return $this->election_id;
    }

    public function setElectionId(int $election_id): static
    {
        $this->election_id = $election_id;
        return $this;
    }

    public function getElectionResultId(): int
    {
        return $this->election_result_id;
    }

    public function setElectionResultId(int $election_result_id): static
    {
        $this->election_result_id = $election_result_id;
        return $this;
    }

    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    public function setAssemblyId(int $assembly_id): static
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    public function toArray(): array
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
