<?php
namespace Althingi\Utils;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Rend\Event\ApplicationErrorHandler;

class RequestErrorHandler implements ListenerAggregateInterface
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, new ApplicationErrorHandler());
    }

    public function detach(EventManagerInterface $events)
    {
        // TODO: Implement detach() method.
    }
}
