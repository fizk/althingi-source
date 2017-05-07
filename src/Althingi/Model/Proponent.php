<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
class Proponent extends Congressman
{
    /** @var  string */
    private $minister;

    /**
     * @return string
     */
    public function getMinister(): ?string
    {
        return $this->minister;
    }

    /**
     * @param string $minister
     * @return Proponent
     */
    public function setMinister(string $minister = null): Proponent
    {
        $this->minister = $minister;
        return $this;
    }

    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            ['minister' => $this->minister]
        );
    }
}
