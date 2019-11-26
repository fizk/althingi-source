<?php

namespace Althingi\Controller;

use Althingi\Injector\ServiceSearchIssueAwareInterface;
use Rend\View\Model\CollectionModel;
use Althingi\Service;
use Zend\Mvc\Controller\AbstractActionController;

class SearchIssueController extends AbstractActionController implements
    ServiceSearchIssueAwareInterface
{
    /** @var  \Althingi\Service\SearchIssue */
    private $issueSearchService;

    /**
     * @return CollectionModel
     * @output \Althingi\Model\Issue[]
     * @query leit
     * @206 Success
     */
    public function issueAction()
    {
        $assemblyId = $this->params('id');
        $query = $this->params()->fromQuery('leit');
        $committees = $this->issueSearchService->fetchByAssembly($query, $assemblyId);

        return (new CollectionModel($committees))
            ->setStatus(206)
            ->setRange(0, count($committees), count($committees));
    }

    /**
     * @param \Althingi\Service\SearchIssue $issue
     * @return $this
     */
    public function setSearchIssueService(Service\SearchIssue $issue)
    {
        $this->issueSearchService = $issue;
        return $this;
    }
}
