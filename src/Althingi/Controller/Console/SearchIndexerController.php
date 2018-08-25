<?php

namespace Althingi\Controller\Console;

use Althingi\ElasticSearchActions\Add;
use Althingi\Lib\ElasticSearchClientAwareInterface;
use Althingi\Lib\ServiceIssueAwareInterface;
use Althingi\Lib\ServiceSpeechAwareInterface;
use Althingi\Presenters\IndexableIssuePresenter;
use Althingi\Presenters\IndexableSpeechPresenter;
use Althingi\Service\Issue;
use Althingi\Service\Speech;
use Althingi\ServiceEvents\AddEvent;
use Elasticsearch\Client;
use Zend\Mvc\Controller\AbstractActionController;

class SearchIndexerController extends AbstractActionController implements
    ServiceSpeechAwareInterface,
    ServiceIssueAwareInterface,
    ElasticSearchClientAwareInterface
{
    /** @var  \Althingi\Service\Speech */
    private $speechService;

    /** @var  \Althingi\Service\Issue */
    private $issueService;

    /** @var  \Elasticsearch\Client */
    private $elasticSearchClient;

    public function speechAction()
    {
        $elasticSearchAdd = new Add($this->elasticSearchClient);
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
        $elasticSearchAdd = new Add($this->elasticSearchClient);
        foreach ($this->issueService->fetchAll() as $model) {
            $elasticSearchAdd(
                new AddEvent(new IndexableIssuePresenter($model))
            );
        }
    }

    /**
     * @param Speech $speech
     */
    public function setSpeechService(Speech $speech)
    {
        $this->speechService = $speech;
    }

    /**
     * @param \Elasticsearch\Client $client
     */
    public function setElasticSearchClient(Client $client)
    {
        $this->elasticSearchClient = $client;
    }

    /**
     * @param \Althingi\Service\Issue $issue
     */
    public function setIssueService(Issue $issue)
    {
        $this->issueService = $issue;
    }
}
