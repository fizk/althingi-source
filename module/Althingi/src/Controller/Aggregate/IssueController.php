<?php

namespace Althingi\Controller\Aggregate;

use Althingi\Injector\ServiceIssueAwareInterface;
use Althingi\Service\Issue;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use Althingi\Model\IssueTypeAndStatus;
use Althingi\Model\IssueTypeStatus;
use Rend\View\Model\ItemModel;

class IssueController extends AbstractRestfulController implements
    ServiceIssueAwareInterface
{
    use Range;

    /** @var $issueService \Althingi\Service\Issue */
    private $issueService;

    public function get($id)
    {
        $category = $this->params('category', 'A');
        $assemblyId = $this->params('assembly_id', 'A');
        $issueId = $this->params('issue_id', 'A');

        return new ItemModel(
            $this->issueService->get($issueId, $assemblyId, $category)
        );
    }

    /**
     * @return CollectionModel
     * @output \Althingi\Model\Status[]
     */
    public function progressAction()
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);

        return (new CollectionModel($this->issueService->fetchProgress($assemblyId, $issueId)));
    }

    /**
     * @return CollectionModel
     * @output \Althingi\Model\AssemblyStatus[]
     */
    public function countGovernmentAction()
    {
        $assemblyId = $this->params('assembly_id', null);

        return (new CollectionModel($this->issueService->fetchCountByGovernment($assemblyId)));
    }

    /**
     * @return CollectionModel
     * @output \Althingi\Model\AssemblyStatus[]
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


        return (new CollectionModel($groups));
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
