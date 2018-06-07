<?php

namespace Althingi\Controller;

use Althingi\Lib\ServiceIssueAwareInterface;
use Althingi\Service\Issue;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;

/**
 * Class CongressmanSessionController
 * @package Althingi\Controller
 */
class CongressmanIssueController extends AbstractRestfulController implements
    ServiceIssueAwareInterface
{
    /** @var  \Althingi\Service\Issue */
    private $issueService;

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Issue[]
     */
    public function getList()
    {
        $congressmanId = $this->params('congressman_id');

        $issues = $this->issueService->fetchByCongressman($congressmanId);
        $issuesCount = count($issues);

        return (new CollectionModel($issues))
            ->setStatus(206)
            ->setRange(0, $issuesCount, $issuesCount);
    }

    /**
     * @param Issue $issue
     */
    public function setIssueService(Issue $issue)
    {
        $this->issueService = $issue;
    }
}
