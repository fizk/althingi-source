<?php

namespace Althingi\Controller\Aggregate;

use Althingi\Injector\ServiceIssueAwareInterface;
use Althingi\Service\Issue;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use Althingi\Model\IssueTypeAndStatus;
use Althingi\Model\IssueTypeStatus;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\ItemModel;

class IssueController extends AbstractRestfulController implements
    ServiceIssueAwareInterface
{
    use Range;

    /** @var $issueService \Althingi\Service\Issue */
    private $issueService;

    /**
     * @param mixed $id
     * @return ErrorModel|ItemModel|\Rend\View\Model\ModelInterface
     * @output \Althingi\\Althingi\Model\Issue
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $category = $this->params('category', 'A');
        $assemblyId = $this->params('assembly_id', 'A');
        $issueId = $this->params('issue_id', 'A');

        $issue = $this->issueService->get($issueId, $assemblyId, $category);
        return $issue
            ? (new ItemModel($issue))->setStatus(200)
            : (new ErrorModel())->setStatus(404);
    }

    /**
     * @return CollectionModel
     * @output \Althingi\Model\Status[]
     * @206 Success
     */
    public function progressAction()
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);
        $progress = $this->issueService->fetchProgress($assemblyId, $issueId);
        return (new CollectionModel($progress))
            ->setStatus(206)
            ->setRange(0, count($progress), count($progress));
    }

    /**
     * @return CollectionModel
     * @output \Althingi\Model\AssemblyStatus[]
     * @206 Success
     */
    public function countGovernmentAction()
    {
        $assemblyId = $this->params('assembly_id', null);

        $count = $this->issueService->fetchCountByGovernment($assemblyId);
        return (new CollectionModel($count))
            ->setStatus(206)
            ->setRange(0, count($count), count($count));
    }

    /**
     * @return CollectionModel
     * @output \Althingi\Model\AssemblyStatus[]
     * @206 Success
     */
    public function countTypeStatusAction()
    {
        $assemblyId = $this->params('assembly_id', null);
        $category = $this->params()->fromQuery('tegund', 'A');

        $issues = $this->issueService->fetchCountByCategoryAndStatus($assemblyId, $category);

        $groups = array_values(array_reduce($issues, function (array $carry, \Althingi\Model\AssemblyStatus $item) {
            if (! array_key_exists($item->getType(), $carry)) {
                $carry[$item->getType()] = (new IssueTypeAndStatus())
                    ->setType($item->getType())
                    ->setTypeName($item->getTypeName())
                    ->setTypeSubName($item->getTypeSubname());
            }

            $carry[$item->getType()]->addStatus(
                (new IssueTypeStatus())
                    ->setStatus($item->getStatus())
                    ->setCount($item->getCount())
            )->addCount($item->getCount());

            return $carry;
        }, []));


        return (new CollectionModel($groups))
            ->setStatus(206)
            ->setRange(0, count($groups), count($groups));
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
