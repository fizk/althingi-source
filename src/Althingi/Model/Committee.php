<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
class Committee implements ModelInterface
{
    /** @var  int */
    private $committee_id;

    /** @var  string */
    private $name;

    /** @var  int */
    private $first_assembly_id;

    /** @var  int */
    private $last_assembly_id;

    /** @var  string */
    private $abbr_long;

    /** @var  string */
    private $abbr_short;

    /**
     * @return int
     */
    public function getCommitteeId(): int
    {
        return $this->committee_id;
    }

    /**
     * @param int $committee_id
     * @return Committee
     */
    public function setCommitteeId(int $committee_id): Committee
    {
        $this->committee_id = $committee_id;
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
     * @return Committee
     */
    public function setName(string $name = null): Committee
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getFirstAssemblyId(): int
    {
        return $this->first_assembly_id;
    }

    /**
     * @param int $first_assembly_id
     * @return Committee
     */
    public function setFirstAssemblyId(int $first_assembly_id): Committee
    {
        $this->first_assembly_id = $first_assembly_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getLastAssemblyId(): ?int
    {
        return $this->last_assembly_id;
    }

    /**
     * @param int $last_assembly_id
     * @return Committee
     */
    public function setLastAssemblyId(int $last_assembly_id = null): Committee
    {
        $this->last_assembly_id = $last_assembly_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getAbbrLong(): ?string
    {
        return $this->abbr_long;
    }

    /**
     * @param string $abbr_long
     * @return Committee
     */
    public function setAbbrLong(string $abbr_long = null): Committee
    {
        $this->abbr_long = $abbr_long;
        return $this;
    }

    /**
     * @return string
     */
    public function getAbbrShort(): ?string
    {
        return $this->abbr_short;
    }

    /**
     * @param string $abbr_short
     * @return Committee
     */
    public function setAbbrShort(string $abbr_short = null): Committee
    {
        $this->abbr_short = $abbr_short;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'committee_id' => $this->committee_id,
            'name' => $this->name,
            'first_assembly_id' => $this->first_assembly_id,
            'last_assembly_id' => $this->last_assembly_id,
            'abbr_long' => $this->abbr_long,
            'abbr_short' => $this->abbr_short,
        ];
    }
}
