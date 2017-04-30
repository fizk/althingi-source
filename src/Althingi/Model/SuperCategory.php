<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
class SuperCategory implements ModelInterface
{
    /** @var int */
    private $super_category_id;

    /** @var string */
    private $title;

    /**
     * @return int
     */
    public function getSuperCategoryId(): int
    {
        return $this->super_category_id;
    }

    /**
     * @param int $super_category_id
     * @return SuperCategory
     */
    public function setSuperCategoryId(int $super_category_id): SuperCategory
    {
        $this->super_category_id = $super_category_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return SuperCategory
     */
    public function setTitle(string $title = null): SuperCategory
    {
        $this->title = $title;
        return $this;
    }

    public function toArray()
    {
        return [
            'super_category_id' => $this->super_category_id,
            'title' => $this->title,
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
