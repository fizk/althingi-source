<?php

namespace Althingi\Service;

use Psr\EventDispatcher\EventDispatcherInterface;

trait EventService
{
    private ?EventDispatcherInterface $eventDispatcher = null;

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): static
    {
        $this->eventDispatcher = $eventDispatcher;
        return $this;
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher
            ? $this->eventDispatcher
            : new class implements EventDispatcherInterface
            {
                public function dispatch(object $event)
                {
                }
            };
    }
}
