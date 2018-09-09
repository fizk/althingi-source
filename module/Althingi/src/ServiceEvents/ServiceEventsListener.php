<?php
namespace Althingi\ServiceEvents;

use Althingi\Lib\ElasticSearchAwareInterface;
use Althingi\Lib\LoggerAwareInterface;
use Elasticsearch\Client;
use Psr\Log\LoggerInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Althingi\ElasticSearchActions\Add;
use Althingi\ElasticSearchActions\Update;
use Althingi\ElasticSearchActions\Delete;

class ServiceEventsListener implements
    ListenerAggregateInterface,
    LoggerAwareInterface,
    ElasticSearchAwareInterface
{

    protected $listeners = [];

    /** @var  LoggerInterface */
    protected $logger;

    /** @var \Elasticsearch\Client  */
    protected $client;

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            AddEvent::class,
            new Add($this->getElasticSearchClient(), $this->getLogger())
        );
        $this->listeners[] = $events->attach(
            UpdateEvent::class,
            new Update($this->getElasticSearchClient(), $this->getLogger())
        );
        $this->listeners[] = $events->attach(
            DeleteEvent::class,
            new Delete($this->getElasticSearchClient(), $this->getLogger())
        );
    }

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

    public function setElasticSearchClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    public function getElasticSearchClient()
    {
        return $this->client;
    }
}
