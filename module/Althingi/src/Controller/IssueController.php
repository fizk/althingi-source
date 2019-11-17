<?php

namespace Althingi\Controller;

use Althingi\Injector\ServiceCategoryAwareInterface;
use Althingi\Injector\ServiceConstituencyAwareInterface;
use Althingi\Injector\StoreCategoryAwareInterface;
use Althingi\Service\Constituency;
use Althingi\Store\Category;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use Althingi\Utils\Transformer;
use Althingi\Injector\ServiceSearchIssueAwareInterface;
use Althingi\Injector\StoreIssueAwareInterface;
use Althingi\Injector\ServiceAssemblyAwareInterface;
use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Injector\ServiceDocumentAwareInterface;
use Althingi\Injector\ServiceIssueAwareInterface;
use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Injector\ServiceSpeechAwareInterface;
use Althingi\Injector\ServiceVoteAwareInterface;
use Althingi\Form;
use Althingi\Model;
use Althingi\Service;
use Althingi\Store;
use Rend\View\Model\ModelInterface;

class IssueController extends AbstractRestfulController implements
    ServiceIssueAwareInterface,
    ServiceAssemblyAwareInterface,
    ServiceCategoryAwareInterface,
    StoreIssueAwareInterface,
    StoreCategoryAwareInterface
{
    use Range;

    /** @var  \Althingi\Service\Issue */
    private $issueService;

    /** @var  \Althingi\Service\Assembly */
    private $assemblyService;

    /** @var  \Althingi\Service\Category */
    private $categoryService;

    /** @var  \Althingi\Store\Issue */
    private $issueStore;

    /** @var  \Althingi\Store\Category */
    private $categoryStore;

    /**
     * Get one issie.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\IssueProperties
     * @query category
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $assemblyId = $this->params('id', 0);
        $issueId = $this->params('issue_id', 0);
        $category = strtoupper($this->params('category', 'a'));

        $issue = $this->issueStore->get($assemblyId, $issueId, $category);
        return $issue
            ? (new ItemModel($issue))->setStatus(200)
            : (new ErrorModel('Resource Not Found'))->setStatus(404);
    }

    /**
     * Get issues per assembly.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\IssueProperties[]
     * @query type [string]
     * @query order [string]
     * @206 Success
     */
    public function getList()
    {
        $assemblyId = $this->params('id', null);
        $typeQuery = $this->params()->fromQuery('type', null);
        $kindQuery = $this->params()->fromQuery('kind', null);
        $orderQuery = $this->params()->fromQuery('order', null);
        $types = $typeQuery ? explode(',', $typeQuery) : [];
        $kinds = $kindQuery ? explode(',', $kindQuery) : [];
        $categories = array_map(function ($category) {
            return strtoupper($category);
        }, explode(',', $this->params('category', 'a,b')));

        $count = $this->issueStore->countByAssembly($assemblyId, $types, $kinds, $categories);
        $range = $this->getRange($this->getRequest(), $count);
        $issues = $this->issueStore->fetchByAssembly(
            $assemblyId,
            $range->getFrom(),
            $range->getSize(),
            $types,
            $kinds,
            $categories
        );

        return (new CollectionModel($issues))
            ->setStatus(206)
            ->setRange($range->getFrom(), $range->getFrom() + count($issues), $count);
    }

    /**
     * Save one issue.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Issue
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $category = strtoupper($this->params('category', 'a'));
        $issueId = $id;

        $form = (new Form\Issue())
            ->setData(array_merge(
                $data,
                ['assembly_id' => $assemblyId, 'issue_id' => $issueId, 'category' => $category]
            ));

        if ($form->isValid()) {
            $affectedRows = $this->issueService->save($form->getObject());
            return (new EmptyModel())->setStatus($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * Update one issue.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Issue
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch($id, $data)
    {
        $assemblyId = $this->params('id');
        $category = strtoupper($this->params('category', 'a'));
        $issue = $this->issueService->get($id, $assemblyId, $category);

        if (! $issue) {
            return (new ErrorModel('Resource Not Found'))->setStatus(404);
        }

        $form = new Form\Issue();
        $form->setObject($issue);
        $form->setData($data);

        if ($form->isValid()) {
            $this->issueService->update($form->getObject());
            return (new EmptyModel())->setStatus(205);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Status[]
     * @206 Success
     */
    public function progressAction()
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $category = strtoupper($this->params('category', 'a'));

        $collection = $this->issueService->fetchProgress($assemblyId, $issueId, $category);

        return (new CollectionModel($collection))
            ->setStatus(206)
            ->setRange(0, count($collection), count($collection));
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\IssueValue[]
     * @query rod asc|desc
     * @query fjoldi [number]
     * @206 Success
     */
    public function speechTimesAction()
    {
        $assemblyId = $this->params('id', 0);
        $order = $this->params()->fromQuery('rod', 'desc');
        $size = $this->params()->fromQuery('fjoldi', 5);
        $categories = array_map(function ($category) {
            return strtoupper($category);
        }, explode(',', $this->params('category', 'a,b')));


        $collection = $this->issueStore->fetchByAssemblyAndSpeechTime(
            $assemblyId,
            $size,
            $order === 'desc' ? -1 : 1,
            $categories
        );

        return (new CollectionModel($collection))
            ->setStatus(206)
            ->setRange(0, count($collection), count($collection));
    }

    /**
     * @return ModelInterface
     * @output \Althingi\Model\IssuesStatusProperties
     * @200 Success
     */
    public function statisticsAction()
    {
        $assemblyId = $this->params('id');

        $object = (new Model\IssuesStatusProperties())
            ->setBills($this->issueStore->fetchNonGovernmentBillStatisticsByAssembly($assemblyId))
            ->setGovernmentBills($this->issueStore->fetchGovernmentBillStatisticsByAssembly($assemblyId))
            ->setProposals($this->issueStore->fetchProposalStatisticsByAssembly($assemblyId))
            ->setTypes($this->issueStore->fetchCountByCategory($assemblyId))
            ->setCategories($this->categoryStore->fetchByAssembly($assemblyId))
        ;

        return (new ItemModel($object))
            ->setStatus(200);
    }

    /**
     * Get all issues where proponents are in a given party.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\IssueProperties[]
     * @query type [string]
     * @206 Success
     */
    public function fetchPartyAction()
    {
        $assemblyId = $this->params('id', 0);
        $partyId = $this->params('party_id', 0);
        $typeQuery = $this->params()->fromQuery('type', null);
        $types = $typeQuery ? explode(',', $typeQuery) : [];

        $count = $this->issueStore->countByParty($assemblyId, $partyId, $types);
        $range = $this->getRange($this->getRequest(), $count);

        $issues = $this->issueStore->fetchByParty(
            $assemblyId,
            $partyId,
            $types,
            $range->getFrom(),
            $range->getSize()
        );

        return (new CollectionModel($issues))
            ->setRange($range->getFrom(), $range->getFrom() + count($issues), $count);
    }

    /**
     * List options for Assembly collection.
     *
     * @return \Rend\View\Model\ModelInterface
     * @200 Success
     */
    public function optionsList()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS'])
            ->setOption('Access-Control-Allow-Origin', '*')
            ->setOption('Access-Control-Allow-Headers', 'Range');
    }

    /**
     * List options for Assembly entry.
     *
     * @return \Rend\View\Model\ModelInterface
     * @200 Success
     */
    public function options()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS', 'PUT', 'PATCH'])
            ->setOption('Access-Control-Allow-Origin', '*')
            ->setOption('Access-Control-Allow-Headers', 'Range');
    }

    /**
     * Set service.
     *
     * @param \Althingi\Service\Issue $issue
     * @return $this;
     */
    public function setIssueService(Service\Issue $issue)
    {
        $this->issueService = $issue;
        return $this;
    }

    /**
     * @param \Althingi\Service\Assembly $assembly
     * @return $this
     */
    public function setAssemblyService(Service\Assembly $assembly)
    {
        $this->assemblyService = $assembly;
        return $this;
    }

    /**
     * @param \Althingi\Store\Issue $issue
     * @return $this
     */
    public function setIssueStore(Store\Issue $issue)
    {
        $this->issueStore = $issue;
        return $this;
    }

    /**
     * @param \Althingi\Store\Category $category
     * @return $this;
     */
    public function setCategoryStore(Category $category)
    {
        $this->categoryStore = $category;
        return $this;
    }

    /**
     * @param \Althingi\Service\Category $category
     * @return $this;
     */
    public function setCategoryService(Service\Category $category)
    {
        $this->categoryService = $category;
        return $this;
    }
}
