<?php

namespace Althingi\Controller\Console;

use Althingi\ElasticSearchActions\Add;
use Althingi\Lib\ElasticSearchClientAwareInterface;
use Althingi\Lib\LoggerAwareInterface;
use Althingi\Lib\ServiceIssueAwareInterface;
use Althingi\Lib\ServiceSpeechAwareInterface;
use Althingi\Presenters\IndexableIssuePresenter;
use Althingi\Presenters\IndexableSpeechPresenter;
use Althingi\Service\Issue;
use Althingi\Service\Speech;
use Althingi\Events\AddEvent;
use Elasticsearch\Client;
use Psr\Log\LoggerInterface;
use Zend\Mvc\Controller\AbstractActionController;

class SearchIndexerController extends AbstractActionController implements
    ServiceSpeechAwareInterface,
    ServiceIssueAwareInterface,
    ElasticSearchClientAwareInterface,
    LoggerAwareInterface
{
    /** @var  \Althingi\Service\Speech */
    private $speechService;

    /** @var  \Althingi\Service\Issue */
    private $issueService;

    /** @var  \Elasticsearch\Client */
    private $elasticSearchClient;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    public function speechAction()
    {
        $elasticSearchAdd = new Add($this->elasticSearchClient, $this->logger);
        foreach ($this->speechService->fetchAll() as $model) {
            $elasticSearchAdd(
                new AddEvent(new IndexableSpeechPresenter($model))
            );
        }
    }

    /**
     * @todo add $category to fetchAll() | [A, B]
     */
    public function issueAction()
    {
        $elasticSearchAdd = new Add($this->elasticSearchClient, $this->logger);
        foreach ($this->issueService->fetchAll() as $model) {
            $elasticSearchAdd(
                new AddEvent(new IndexableIssuePresenter($model))
            );
        }
    }

    /**
     * @param Speech $speech
     * @return $this
     */
    public function setSpeechService(Speech $speech)
    {
        $this->speechService = $speech;
        return $this;
    }

    /**
     * @param \Elasticsearch\Client $client
     * @return $this
     */
    public function setElasticSearchClient(Client $client)
    {
        $this->elasticSearchClient = $client;
        return $this;
    }

    /**
     * @param \Althingi\Service\Issue $issue
     * @return $this
     */
    public function setIssueService(Issue $issue)
    {
        $this->issueService = $issue;
        return $this;
    }

    /**
     * @param LoggerInterface $logger
     * @return mixed
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
