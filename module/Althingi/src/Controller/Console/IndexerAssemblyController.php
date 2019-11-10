<?php

namespace Althingi\Controller\Console;

use Althingi\Injector\LoggerAwareInterface;
use Althingi\Injector\QueueAwareInterface;
use Althingi\Injector\ServiceAssemblyAwareInterface;
use Althingi\Presenters\IndexableAssemblyPresenter;
use Althingi\Service\Assembly;
use Althingi\QueueActions\Add;
use Althingi\Events\AddEvent;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Log\LoggerInterface;
use Zend\Mvc\Controller\AbstractActionController;

class IndexerAssemblyController extends AbstractActionController implements
    ServiceAssemblyAwareInterface,
    LoggerAwareInterface,
    QueueAwareInterface
{

    /** @var  \Althingi\Service\Assembly */
    private $assemblyService;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \PhpAmqpLib\Connection\AMQPStreamConnection  */
    protected $client;

    public function assemblyAction()
    {
        $addEvent = new Add($this->getQueue(), $this->getLogger());

        $assemblies = $this->assemblyService->fetchAll();
        foreach ($assemblies as $assembly) {
            $addEvent(new AddEvent(new IndexableAssemblyPresenter($assembly), ['rows' => 1]));
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
     * @param \Althingi\Service\Assembly $assembly
     * @return $this;
     */
    public function setAssemblyService(Assembly $assembly)
    {
        $this->assemblyService = $assembly;
        return $this;
    }
}
