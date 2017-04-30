<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
class CongressmanAndParties implements ModelInterface
{
    /** @var  \Althingi\Model\Congressman */
    private $congressman;

    /** @var  \Althingi\Model\Party[] */
    private $parties;

    /**
     * @return Congressman
     */
    public function getCongressman(): Congressman
    {
        return $this->congressman;
    }

    /**
     * @param Congressman $congressman
     * @return CongressmanAndParties
     */
    public function setCongressman(Congressman $congressman): CongressmanAndParties
    {
        $this->congressman = $congressman;
        return $this;
    }

    /**
     * @return \Althingi\Model\Party[]
     */
    public function getParties(): array
    {
        return $this->parties;
    }

    /**
     * @param \Althingi\Model\Party[] $parties
     * @return CongressmanAndParties
     */
    public function setParties(array $parties = []): CongressmanAndParties
    {
        $this->parties = $parties;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            $this->congressman->toArray(),
            ['parties' => $this->getParties()]
        );
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
