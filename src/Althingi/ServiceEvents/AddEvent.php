<?php

namespace Althingi\ServiceEvents;

use Zend\EventManager\Event;
use Althingi\Model\ModelInterface;
use Zend\Hydrator\HydratorInterface;

class AddEvent extends Event implements ModelAndHydrator
{
    /** @var  \Althingi\Model\ModelInterface */
    private $model;

    /** @var  \Zend\Hydrator\HydratorInterface */
    private $hydrator;

    public function __construct(ModelInterface $mode, HydratorInterface $hydrator)
    {
        $this->setName('add');
        $this->setModel($mode);
        $this->setHydrator($hydrator);
    }

    /**
     * @return ModelInterface
     */
    public function getModel(): ModelInterface
    {
        return $this->model;
    }

    /**
     * @param ModelInterface $model
     * @return ModelAndHydrator
     */
    public function setModel(ModelInterface $model): ModelAndHydrator
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return HydratorInterface
     */
    public function getHydrator(): HydratorInterface
    {
        return $this->hydrator;
    }

    /**
     * @param HydratorInterface $hydrator
     * @return ModelAndHydrator
     */
    public function setHydrator(HydratorInterface $hydrator): ModelAndHydrator
    {
        $this->hydrator = $hydrator;
        return $this;
    }
}
