<?php

namespace Althingi\Model;

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

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
