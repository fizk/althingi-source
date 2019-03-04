<?php
namespace Althingi\ElasticSearchActions;

use Althingi\Events\EventsListener;
use Althingi\Lib\ElasticSearchAwareInterface;
use Elasticsearch\Client;
use Zend\EventManager\EventManagerInterface;
use Althingi\Events\AddEvent;
use Althingi\Events\UpdateEvent;
use Althingi\Events\DeleteEvent;

class ElasticSearchEventsListener extends EventsListener implements ElasticSearchAwareInterface
{
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
