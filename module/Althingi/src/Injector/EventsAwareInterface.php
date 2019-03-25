<?php

namespace Althingi\Injector;

use Zend\EventManager\EventManagerInterface;

interface EventsAwareInterface
{
    public function setEventManager(EventManagerInterface $events);

    public function getEventManager();
}
