<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
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
    public function setCount(int $count = null): AssemblyStatus
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
    public function setType(string $type = null): AssemblyStatus
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
    public function setTypeName(string $type_name = null): AssemblyStatus
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
    public function setTypeSubname(string $type_subname = null): AssemblyStatus
    {
        $this->type_subname = $type_subname;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'count' => $this->count,
            'type' => $this->type,
            'type_name' => $this->type_name,
            'type_subname' => $this->type_subname,
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
