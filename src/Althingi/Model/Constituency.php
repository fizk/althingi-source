<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
class Constituency implements ModelInterface
{
    /** @var  int */
    private $constituency_id;

    /** @var string|null */
    private $name = null;

    /** @var string|null */
    private $abbr_short = null;

    /** @var string|null */
    private $abbr_long = null;

    /** @var string|null */
    private $description = null;

    /**
     * @return int
     */
    public function getConstituencyId(): int
    {
        return $this->constituency_id;
    }

    /**
     * @param int $constituency_id
     * @return Constituency
     */
    public function setConstituencyId(int $constituency_id): Constituency
    {
        $this->constituency_id = $constituency_id;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     * @return Constituency
     */
    public function setName(string $name = null): Constituency
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getAbbrShort(): ?string
    {
        return $this->abbr_short;
    }

    /**
     * @param null|string $abbr_short
     * @return Constituency
     */
    public function setAbbrShort(string $abbr_short = null): Constituency
    {
        $this->abbr_short = $abbr_short;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getAbbrLong(): ?string
    {
        return $this->abbr_long;
    }

    /**
     * @param null|string $abbr_long
     * @return Constituency
     */
    public function setAbbrLong(string $abbr_long = null): Constituency
    {
        $this->abbr_long = $abbr_long;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     * @return Constituency
     */
    public function setDescription(string $description = null): Constituency
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'constituency_id' => $this->constituency_id,
            'name' => $this->name,
            'abbr_short' => $this->abbr_short,
            'abbr_long' => $this->abbr_long,
            'description' => $this->description,
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
