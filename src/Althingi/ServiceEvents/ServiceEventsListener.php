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
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('add', new Add($this->getElasticSearchClient()));
        $this->listeners[] = $events->attach('update', new Update($this->getElasticSearchClient()));
        $this->listeners[] = $events->attach('delete', new Delete($this->getElasticSearchClient()));
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
     * @return mixed
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
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
    }

    public function getElasticSearchClient()
    {
        return $this->client;
    }
}
