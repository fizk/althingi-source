<?php

namespace Althingi\Controller\Aggregate;

use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Lib\ServiceConstituencyAwareInterface;
use Althingi\Lib\ServiceIssueAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Service\Congressman;
use Althingi\Service\Constituency;
use Althingi\Service\Issue;
use Althingi\Service\Party;
use Althingi\Utils\CategoryParam;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use DateTime;

class IssueController extends AbstractRestfulController implements
    ServiceIssueAwareInterface
{
    use Range;

    use CategoryParam;


    /** @var $issueService \Althingi\Service\Issue */
    private $issueService;


    public function progressAction()
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);

        return (new CollectionModel($this->issueService->fetchProgress($assemblyId, $issueId)));
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
}
