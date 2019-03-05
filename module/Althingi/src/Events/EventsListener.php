<?php
namespace Althingi\Events;

use Psr\Log\LoggerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Althingi\Lib\LoggerAwareInterface;

abstract class EventsListener implements
    ListenerAggregateInterface,
    LoggerAwareInterface
{
    protected $listeners = [];

    /** @var  LoggerInterface */
    protected $logger;

    /**
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
