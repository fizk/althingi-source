<?php

namespace Althingi\Injector;

use Psr\EventDispatcher\EventDispatcherInterface;

interface EventsAwareInterface
{
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): self;

    public function getEventDispatcher(): EventDispatcherInterface;
}
