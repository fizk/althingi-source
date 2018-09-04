<?php

namespace Althingi\Lib;

use Zend\EventManager\EventManagerInterface;

interface EventsAwareInterface
{
    public function setEventManager(EventManagerInterface $events);

    public function getEventManager();
}
