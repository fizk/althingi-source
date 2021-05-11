<?php

namespace Althingi\Model;

class AssemblyStatus implements ModelInterface
{
    /** @var  int */
    private $count;

    /** @var  string */
    private $type;

    /** @var  string */
    private $type_name;

    /** @var  string */
    private $type_subname;

    /** @var string */
    private $status;

    /** @var string */
    private $category;

    /**
     * @return int
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return AssemblyStatus|null
     */
    public function setCount(?int $count): self
    {
        $this->count = $count;
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
     * @return AssemblyStatus
     */
    public function setType(?string $type): self
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
     * @return AssemblyStatus
     */
    public function setTypeName(?string $type_name): self
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
     * @return AssemblyStatus
     */
    public function setTypeSubname(?string $type_subname): self
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
     * @return AssemblyStatus
     */
    public function setStatus(?string $status): self
    {
        $this->status = $status;
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
     * @return AssemblyStatus
     */
    public function setCategory(?string $category): self
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'count' => $this->count,
            'type' => $this->type,
            'type_name' => $this->type_name,
            'type_subname' => $this->type_subname,
            'status' => $this->status,
            'category' => $this->category,
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
