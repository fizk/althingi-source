<?php
namespace Althingi\Utils;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\MvcEvent;
use Rend\Event\ApplicationErrorHandler;

class RequestErrorHandler implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, new ApplicationErrorHandler(), $priority);
    }

    public function detach(EventManagerInterface $events)
    {
        // TODO: Implement detach() method.
    }
}
