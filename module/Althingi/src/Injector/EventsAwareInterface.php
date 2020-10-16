<?php

namespace Althingi\Injector;

use Laminas\EventManager\EventManagerInterface;

interface EventsAwareInterface
{
    public function setEventManager(EventManagerInterface $events);

    public function getEventManager();
}
