<?php

namespace Althingi\Controller\Console;

use Althingi\Injector\LoggerAwareInterface;
use Althingi\Injector\QueueAwareInterface;
use Althingi\Injector\ServiceAssemblyAwareInterface;
use Althingi\Injector\ServiceDocumentAwareInterface;
use Althingi\Injector\ServiceIssueAwareInterface;
use Althingi\Injector\ServiceIssueCategoryAwareInterface;
use Althingi\Injector\ServiceProponentAwareInterface;
use Althingi\Injector\ServiceSessionAwareInterface;
use Althingi\Injector\ServiceSpeechAwareInterface;
use Althingi\Injector\ServiceVoteAwareInterface;
use Althingi\Injector\ServiceVoteItemAwareInterface;
use Althingi\Presenters\IndexableAssemblyPresenter;
use Althingi\Presenters\IndexableCongressmanDocumentPresenter;
use Althingi\Presenters\IndexableDocumentPresenter;
use Althingi\Presenters\IndexableIssueCategoryPresenter;
use Althingi\Presenters\IndexableIssuePresenter;
use Althingi\Presenters\IndexableSessionPresenter;
use Althingi\Presenters\IndexableSpeechPresenter;
use Althingi\Presenters\IndexableVoteItemPresenter;
use Althingi\Presenters\IndexableVotePresenter;
use Althingi\Service\Assembly;
use Althingi\Service\CongressmanDocument;
use Althingi\Service\Document;
use Althingi\Service\Issue;
use Althingi\Service\IssueCategory;
use Althingi\Service\Session;
use Althingi\Service\Speech;
use Althingi\Service\Vote;
use Althingi\Service\VoteItem;
use Althingi\QueueActions\Add;
use Althingi\Events\AddEvent;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Log\LoggerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ProgressBar\ProgressBar;
use Zend\ProgressBar\Adapter\Console;

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
