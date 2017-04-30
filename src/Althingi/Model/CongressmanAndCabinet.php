<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
class CongressmanAndCabinet extends Congressman
{
    /** @var  string */
    private $title;

    /** @var  \DateTime */
    private $date;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return CongressmanAndCabinet
     */
    public function setTitle(string $title): CongressmanAndCabinet
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return CongressmanAndCabinet
     */
    public function setDate(\DateTime $date = null): CongressmanAndCabinet
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            [
                'title' => $this->title,
                'date' => $this->date ? $this->date->format('Y-m-d') : null,
            ]
        );
    }
}
