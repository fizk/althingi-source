<?php

namespace Althingi\Service;

use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;

trait EventService
{
    protected ?EventManagerInterface $events = null;

    public function setEventManager(EventManagerInterface $events)
    {
        $this->events = $events;
        return $this;
    }

    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }
}
