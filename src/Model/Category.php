<?php

namespace Althingi\Model;

class Category implements ModelInterface
{
    /** @var  int */
    private $category_id;

    /** @var  int */
    private $super_category_id;

    /** @var  string */
    private $title;

    /** @var  string */
    private $description;

    /**
     * @return int
     */
    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    /**
     * @param int $category_id
     * @return Category
     */
    public function setCategoryId(int $category_id): Category
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
     * @return Category
     */
    public function setSuperCategoryId(int $super_category_id): Category
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
     * @return Category
     */
    public function setTitle(string $title = null): Category
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Category
     */
    public function setDescription(string $description = null): Category
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'category_id' => $this->category_id,
            'super_category_id' => $this->super_category_id,
            'title' => $this->title,
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
