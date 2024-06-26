<?php

namespace Althingi\Injector;

use Psr\EventDispatcher\EventDispatcherInterface;

interface EventsAwareInterface
{
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): static;

    public function getEventDispatcher(): EventDispatcherInterface;
}
