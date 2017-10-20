<?php
namespace Althingi\ServiceEvents;

use Althingi\Model\ModelInterface;
use Zend\Hydrator\HydratorInterface;

interface ModelAndHydrator
{
    /**
     * @return ModelInterface
     */
    public function getModel(): ModelInterface;

    /**
     * @param \Althingi\Model\ModelInterface $model
     * @return ModelAndHydrator
     */
    public function setModel(ModelInterface $model): ModelAndHydrator;

    /**
     * @return \Zend\Hydrator\HydratorInterface
     */
    public function getHydrator(): HydratorInterface;

    /**
     * @param \Zend\Hydrator\HydratorInterface $hydrator
     * @return ModelAndHydrator
     */
    public function setHydrator(HydratorInterface $hydrator): ModelAndHydrator;
}
