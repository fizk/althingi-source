<?php
namespace Althingi\QueueActions;

use Althingi\Events\EventsListener;

use Althingi\Lib\QueueAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Events\DeleteEvent;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class QueueEventsListener extends EventsListener implements QueueAwareInterface
{
    /** @var \PhpAmqpLib\Connection\AMQPStreamConnection  */
    protected $client;

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            AddEvent::class,
            new Add($this->getQueue(), $this->getLogger())
        );
        $this->listeners[] = $events->attach(
            UpdateEvent::class,
            new Update($this->getQueue(), $this->getLogger())
        );
        $this->listeners[] = $events->attach(
            DeleteEvent::class,
            new Delete($this->getQueue(), $this->getLogger())
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
}
