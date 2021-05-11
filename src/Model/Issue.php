<?php

namespace Althingi\Model;

class Issue implements ModelInterface
{
    private $issue_id;
    private $assembly_id;
    private ?int $congressman_id = null;
    private $category;
    private ?string $name = null;
    private ?string $sub_name = null;
    private ?string $type = null;
    private ?string $type_name = null;
    private ?string $type_subname = null;
    private ?string $status = null;
    private ?string $question = null;
    private ?string $goal = null;
    private ?string $major_changes = null;
    private ?string $changes_in_law = null;
    private ?string $costs_and_revenues = null;
    private ?string $deliveries = null;
    private ?string $additional_information = null;

    public function getIssueId(): int
    {
        return $this->issue_id;
    }

    public function setIssueId(int $issue_id): self
    {
        $this->issue_id = $issue_id;
        return $this;
    }

    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    public function setAssemblyId(int $assembly_id): self
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    public function getCongressmanId(): ?int
    {
        return $this->congressman_id;
    }

    public function setCongressmanId(?int $congressman_id = null): self
    {
        $this->congressman_id = $congressman_id;
        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name = null): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSubName(): ?string
    {
        return $this->sub_name;
    }

    public function setSubName(?string $sub_name = null): self
    {
        $this->sub_name = $sub_name;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type = null): self
    {
        $this->type = $type;
        return $this;
    }

    public function getTypeName(): ?string
    {
        return $this->type_name;
    }

    public function setTypeName(?string $type_name = null): self
    {
        $this->type_name = $type_name;
        return $this;
    }

    public function getTypeSubname(): ?string
    {
        return $this->type_subname;
    }

    public function setTypeSubname(?string $type_subname = null): self
    {
        $this->type_subname = $type_subname;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status = null): self
    {
        $this->status = $status;
        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(?string $question = null): self
    {
        $this->question = $question;
        return $this;
    }

    public function getGoal(): ?string
    {
        return $this->goal;
    }

    public function setGoal(?string $goal = null): self
    {
        $this->goal = $goal;
        return $this;
    }

    public function getMajorChanges(): ?string
    {
        return $this->major_changes;
    }

    public function setMajorChanges(?string $major_changes = null): self
    {
        $this->major_changes = $major_changes;
        return $this;
    }

    public function getChangesInLaw(): ?string
    {
        return $this->changes_in_law;
    }

    public function setChangesInLaw(?string $changes_in_law = null): self
    {
        $this->changes_in_law = $changes_in_law;
        return $this;
    }

    public function getCostsAndRevenues(): ?string
    {
        return $this->costs_and_revenues;
    }

    public function setCostsAndRevenues(?string $costs_and_revenues = null): self
    {
        $this->costs_and_revenues = $costs_and_revenues;
        return $this;
    }

    public function getDeliveries(): ?string
    {
        return $this->deliveries;
    }

    public function setDeliveries(?string $deliveries = null): self
    {
        $this->deliveries = $deliveries;
        return $this;
    }

    public function getAdditionalInformation(): ?string
    {
        return $this->additional_information;
    }

    public function setAdditionalInformation(?string $additional_information = null): self
    {
        $this->additional_information = $additional_information;
        return $this;
    }

    public function isA()
    {
        return $this->category === 'A';
    }

    public function isB()
    {
        return $this->category === 'B';
    }

    public function toArray(): array
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

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
