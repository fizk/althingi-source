<?php

namespace Althingi\Model;

class Issue implements ModelInterface
{
    /** @var int */
    private $issue_id;

    /** @var int */
    private $assembly_id;

    /** @var int */
    private $congressman_id;

    /** @var string */
    private $category;

    /** @var string */
    private $name;

    /** @var string */
    private $sub_name;

    /** @var string */
    private $type;

    /** @var string */
    private $type_name;

    /** @var string */
    private $type_subname;

    /** @var string */
    private $status;

    /** @var string */
    private $question;

    /** @var string */
    private $goal;

    /** @var string */
    private $major_changes;

    /** @var string */
    private $changes_in_law;

    /** @var string */
    private $costs_and_revenues;

    /** @var string */
    private $deliveries;

    /** @var string */
    private $additional_information;

    /**
     * @return int
     */
    public function getIssueId(): int
    {
        return $this->issue_id;
    }

    /**
     * @param int $issue_id
     * @return Issue
     */
    public function setIssueId(int $issue_id): Issue
    {
        $this->issue_id = $issue_id;
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
     * @return Issue
     */
    public function setAssemblyId(int $assembly_id): Issue
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getCongressmanId(): ?int
    {
        return $this->congressman_id;
    }

    /**
     * @param int $congressman_id
     * @return Issue
     */
    public function setCongressmanId(int $congressman_id = null): Issue
    {
        $this->congressman_id = $congressman_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return Issue
     */
    public function setCategory(string $category = null): Issue
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Issue
     */
    public function setName(string $name = null): Issue
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubName(): ?string
    {
        return $this->sub_name;
    }

    /**
     * @param string $sub_name
     * @return Issue
     */
    public function setSubName(string $sub_name = null): Issue
    {
        $this->sub_name = $sub_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Issue
     */
    public function setType(string $type = null): Issue
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeName(): ?string
    {
        return $this->type_name;
    }

    /**
     * @param string $type_name
     * @return Issue
     */
    public function setTypeName(string $type_name = null): Issue
    {
        $this->type_name = $type_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeSubname(): ?string
    {
        return $this->type_subname;
    }

    /**
     * @param string $type_subname
     * @return Issue
     */
    public function setTypeSubname(string $type_subname = null): Issue
    {
        $this->type_subname = $type_subname;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Issue
     */
    public function setStatus(string $status = null): Issue
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuestion(): ?string
    {
        return $this->question;
    }

    /**
     * @param string $question
     * @return Issue
     */
    public function setQuestion(string $question = null): Issue
    {
        $this->question = $question;
        return $this;
    }

    /**
     * @return string
     */
    public function getGoal(): ?string
    {
        return $this->goal;
    }

    /**
     * @param string $goal
     * @return Issue
     */
    public function setGoal(string $goal = null): Issue
    {
        $this->goal = $goal;
        return $this;
    }

    /**
     * @return string
     */
    public function getMajorChanges(): ?string
    {
        return $this->major_changes;
    }

    /**
     * @param string $major_changes
     * @return Issue
     */
    public function setMajorChanges(string $major_changes = null): Issue
    {
        $this->major_changes = $major_changes;
        return $this;
    }

    /**
     * @return string
     */
    public function getChangesInLaw(): ?string
    {
        return $this->changes_in_law;
    }

    /**
     * @param string $changes_in_law
     * @return Issue
     */
    public function setChangesInLaw(string $changes_in_law = null): Issue
    {
        $this->changes_in_law = $changes_in_law;
        return $this;
    }

    /**
     * @return string
     */
    public function getCostsAndRevenues(): ?string
    {
        return $this->costs_and_revenues;
    }

    /**
     * @param string $costs_and_revenues
     * @return Issue
     */
    public function setCostsAndRevenues(string $costs_and_revenues = null): Issue
    {
        $this->costs_and_revenues = $costs_and_revenues;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveries(): ?string
    {
        return $this->deliveries;
    }

    /**
     * @param string $deliveries
     * @return Issue
     */
    public function setDeliveries(string $deliveries = null): Issue
    {
        $this->deliveries = $deliveries;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalInformation(): ?string
    {
        return $this->additional_information;
    }

    /**
     * @param string $additional_information
     * @return Issue
     */
    public function setAdditionalInformation(string $additional_information = null): Issue
    {
        $this->additional_information = $additional_information;
        return $this;
    }

    public function toArray()
    {
        return [
            'issue_id' => $this->issue_id,
            'assembly_id' => $this->assembly_id,
            'congressman_id' => $this->congressman_id,
            'category' => $this->category,
            'name' => $this->name,
            'sub_name' => $this->sub_name,
            'type' => $this->type,
            'type_name' => $this->type_name,
            'type_subname' => $this->type_subname,
            'status' => $this->status,
            'question' => $this->question,
            'goal' => $this->goal,
            'major_changes' => $this->major_changes,
            'changes_in_law' => $this->changes_in_law,
            'costs_and_revenues' => $this->costs_and_revenues,
            'deliveries' => $this->deliveries,
            'additional_information' => $this->additional_information,
        ];
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
