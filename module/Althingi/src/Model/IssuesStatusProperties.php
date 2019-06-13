<?php

namespace Althingi\Model;

class IssuesStatusProperties implements ModelInterface
{
    /** @var  \Althingi\Model\IssueTypeStatus[] */
    private $bills = [];

    /** @var  \Althingi\Model\IssueTypeStatus[] */
    private $government_bills = [];

    /** @var  \Althingi\Model\IssueTypeStatus[] */
    private $proposals = [];

    /** @var  \Althingi\Model\AssemblyStatus[] */
    private $types = [];

    /** @var  \Althingi\Model\CategoryAndCount[] */
    private $categories = [];


    /**
     * @return IssueTypeStatus[]
     */
    public function getBills(): array
    {
        return $this->bills;
    }

    /**
     * @param IssueTypeStatus[] $bills
     * @return IssuesStatusProperties
     */
    public function setBills(array $bills): IssuesStatusProperties
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
     * @return IssuesStatusProperties
     */
    public function setGovernmentBills(array $governmentBills): IssuesStatusProperties
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
     * @return IssuesStatusProperties
     */
    public function setTypes(array $types): IssuesStatusProperties
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
     * @return IssuesStatusProperties
     */
    public function setCategories(array $categories): IssuesStatusProperties
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
     * @return IssuesStatusProperties
     */
    public function setProposals(array $proposals): IssuesStatusProperties
    {
        $this->proposals = $proposals;
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
            'proposals' => $this->proposals,
            'types' => $this->types,
            'categories' => $this->categories,
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
