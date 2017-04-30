<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
class IssueCategoryAndTime implements ModelInterface
{
    /** @var  int */
    private $category_id;

    /** @var  int */
    private $super_category_id;

    /** @var  string */
    private $title;

    /** @var  int */
    private $time;

    /**
     * @return int
     */
    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    /**
     * @param int $category_id
     * @return IssueCategoryAndTime
     */
    public function setCategoryId(int $category_id): IssueCategoryAndTime
    {
        $this->category_id = $category_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getSuperCategoryId(): int
    {
        return $this->super_category_id;
    }

    /**
     * @param int $super_category_id
     * @return IssueCategoryAndTime
     */
    public function setSuperCategoryId(int $super_category_id): IssueCategoryAndTime
    {
        $this->super_category_id = $super_category_id;
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
     * @return IssueCategoryAndTime
     */
    public function setTitle(string $title): IssueCategoryAndTime
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * @param int $time
     * @return IssueCategoryAndTime
     */
    public function setTime(int $time): IssueCategoryAndTime
    {
        $this->time = $time;
        return $this;
    }

    public function toArray()
    {
        return [
            'category_id' => $this->category_id,
            'super_category_id' => $this->super_category_id,
            'title' => $this->title,
            'time' => $this->time,
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
