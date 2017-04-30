<?php
/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/28/16
 * Time: 7:37 PM
 */

namespace Althingi\Model;

class Cabinet implements ModelInterface
{
    /** @var  int */
    private $cabinet_id;

    /** @var  string */
    private $name;

    /** @var  string */
    private $title;

    /**
     * @return int
     */
    public function getCabinetId(): int
    {
        return $this->cabinet_id;
    }

    /**
     * @param int $cabinet_id
     * @return Cabinet
     */
    public function setCabinetId(int $cabinet_id): Cabinet
    {
        $this->cabinet_id = $cabinet_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Cabinet
     */
    public function setName(string $name = null): Cabinet
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Cabinet
     */
    public function setTitle(string $title = null): Cabinet
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'cabinet_id' => $this->cabinet_id,
            'name' => $this->name,
            'title' => $this->title,
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
