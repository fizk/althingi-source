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
    public function setBills(array $bills): self
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
    public function setGovernmentBills(array $governmentBills): self
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
    public function setTypes(array $types): self
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
    public function setCategories(array $categories): self
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
    public function setProposals(array $proposals): self
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
