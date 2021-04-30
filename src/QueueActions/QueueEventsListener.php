<?php
namespace Althingi\QueueActions;

use Althingi\Events\EventsListener;

use Althingi\Injector\MessageBrokerAwareInterface;
use Laminas\EventManager\EventManagerInterface;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Events\DeleteEvent;
use Althingi\Utils\MessageBrokerInterface;

class QueueEventsListener extends EventsListener implements MessageBrokerAwareInterface
{
    protected MessageBrokerInterface $client;
    protected bool $isForced = false;

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            AddEvent::class,
            new Add($this->getMessageBroker(), $this->getLogger(), $this->isForced)
        );
        $this->listeners[] = $events->attach(
            UpdateEvent::class,
            new Update($this->getMessageBroker(), $this->getLogger(), $this->isForced)
        );
        $this->listeners[] = $events->attach(
            DeleteEvent::class,
            new Delete($this->getMessageBroker(), $this->getLogger(), $this->isForced)
        );
    }

    public function setMessageBroker(MessageBrokerInterface $connection)
    {
        $this->client = $connection;
        return $this;
    }

    public function getMessageBroker(): MessageBrokerInterface
    {
        return $this->client;
    }

    /**
     * @return bool
     */
    public function isForced(): bool
    {
        return $this->isForced;
    }

    /**
     * @param bool $isForced
     * @return QueueEventsListener
     */
    public function setIsForced(bool $isForced): QueueEventsListener
    {
        $this->isForced = $isForced;
        return $this;
    }
}
