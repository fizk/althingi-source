<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 8/06/15
 * Time: 9:05 PM
 */

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
     */
    public function getList()
    {
        $congressmanId = $this->params('id');

        $issues = $this->issueService->fetchByCongressman($congressmanId);

        return (new CollectionModel($issues));
    }

    /**
     * @param Issue $issue
     */
    public function setIssueService(Issue $issue)
    {
        $this->issueService = $issue;
    }
}
