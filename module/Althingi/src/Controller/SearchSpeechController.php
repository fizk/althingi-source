<?php

namespace Althingi\Controller;

use Althingi\Injector\ServiceSearchIssueAwareInterface;
use Althingi\Injector\ServiceSearchSpeechAwareInterface;
use Althingi\Service\SearchSpeech;
use Rend\View\Model\CollectionModel;
use Althingi\Service;
use Laminas\Mvc\Controller\AbstractActionController;

class SearchSpeechController extends AbstractActionController implements
    ServiceSearchSpeechAwareInterface
{
    /** @var  \Althingi\Service\SearchSpeech */
    private $speechSearchService;

    /**
     * @return CollectionModel
     * @output \Althingi\Model\Speech[]
     * @query leit
     * @206 Success
     */
    public function assemblyAction()
    {
        $assemblyId = $this->params('id');
        $query = $this->params()->fromQuery('leit');
        $speeches = $this->speechSearchService->fetchByAssembly($query, $assemblyId);
        $speechesCount = count($speeches);

        return (new CollectionModel($speeches))
            ->setStatus(206)
            ->setRange(0, $speechesCount, $speechesCount);
    }

    /**
     * @return CollectionModel
     * @output \Althingi\Model\Speech[]
     * @query leit
     * @206 Success
     */
    public function issueAction()
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $query = $this->params()->fromQuery('leit');
        $speeches = $this->speechSearchService->fetchByIssue($query, $assemblyId, $issueId);
        $speechesCount = count($speeches);

        return (new CollectionModel($speeches))
            ->setStatus(206)
            ->setRange(0, $speechesCount, $speechesCount);
    }

    /**
     * @param \Althingi\Service\SearchSpeech $speech
     * @return $this
     */
    public function setSearchSpeechService(SearchSpeech $speech)
    {
        $this->speechSearchService = $speech;
        return $this;
    }
}
