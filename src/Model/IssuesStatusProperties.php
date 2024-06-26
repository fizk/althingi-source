<?php

namespace Althingi\Model;

class IssuesStatusProperties implements ModelInterface
{
    /** @var \Althingi\Model\IssueTypeStatus[] */
    private array $bills = [];
    /** @var \Althingi\Model\IssueTypeStatus[] */
    private array $government_bills = [];
    /** @var \Althingi\Model\IssueTypeStatus[] */
    private array $proposals = [];
    /** @var \Althingi\Model\AssemblyStatus[] */
    private array $types = [];
    /** @var \Althingi\Model\CategoryAndCount[] */
    private array $categories = [];

    /**
     * @return IssueTypeStatus[]
     */
    public function getBills(): array
    {
        return $this->bills;
    }

    /**
     * @param IssueTypeStatus[] $bills
     */
    public function setBills(array $bills): static
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
     */
    public function setGovernmentBills(array $governmentBills): static
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
     */
    public function setTypes(array $types): static
    {
        $this->types = $types;
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
     */
    public function setCategories(array $categories): static
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @return IssueTypeStatus[]
     */
    public function getProposals(): array
    {
        return $this->proposals;
    }

    /**
     * @param IssueTypeStatus[] $proposals
     */
    public function setProposals(array $proposals): static
    {
        $this->proposals = $proposals;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'bills' => $this->bills,
            'government_bills' => $this->government_bills,
            'proposals' => $this->proposals,
            'types' => $this->types,
            'categories' => $this->categories,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
