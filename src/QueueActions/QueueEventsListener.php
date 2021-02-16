<?php
namespace Althingi\QueueActions;

use Althingi\Events\EventsListener;

use Althingi\Injector\QueueAwareInterface;
use Laminas\EventManager\EventManagerInterface;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Events\DeleteEvent;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class QueueEventsListener extends EventsListener implements QueueAwareInterface
{
    /** @var \PhpAmqpLib\Connection\AMQPStreamConnection  */
    protected $client;

    /** @var bool */
    protected $isForced = false;

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            AddEvent::class,
            new Add($this->getQueue(), $this->getLogger(), $this->isForced)
        );
        $this->listeners[] = $events->attach(
            UpdateEvent::class,
            new Update($this->getQueue(), $this->getLogger(), $this->isForced)
        );
        $this->listeners[] = $events->attach(
            DeleteEvent::class,
            new Delete($this->getQueue(), $this->getLogger(), $this->isForced)
        );
    }

    public function setQueue(AMQPStreamConnection $connection)
    {
        $this->client = $connection;
        return $this;
    }

    public function getQueue(): AMQPStreamConnection
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
