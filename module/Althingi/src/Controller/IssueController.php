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
    ServiceCongressmanAwareInterface,
    ServiceIssueAwareInterface,
    ServicePartyAwareInterface,
    ServiceDocumentAwareInterface,
    ServiceVoteAwareInterface,
    ServiceAssemblyAwareInterface,
    ServiceSpeechAwareInterface,
    ServiceSearchIssueAwareInterface,
    ServiceConstituencyAwareInterface,
    ServiceCategoryAwareInterface,
    StoreIssueAwareInterface,
    StoreCategoryAwareInterface
{
    use Range;

    /** @var  \Althingi\Service\Issue */
    private $issueService;

    /** @var  \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var  \Althingi\Service\Party */
    private $partyService;

    /** @var  \Althingi\Service\Document */
    private $documentService;

    /** @var  \Althingi\Service\Vote */
    private $voteService;

    /** @var  \Althingi\Service\Assembly */
    private $assemblyService;

    /** @var  \Althingi\Service\Speech */
    private $speechService;

    /** @var  \Althingi\Service\SearchIssue */
    private $issueSearchService;

    /** @var  \Althingi\Service\Constituency */
    private $constituencyService;

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
     */
    public function get($id)
    {
        $assemblyId = $this->params('id', 0);
        $issueId = $this->params('issue_id', 0);
        $category = strtoupper($this->params('category', 'a'));

//        $issue = $this->getFromDatabase($assemblyId, $issueId, $category);
        $issue = $this->getFromStore($assemblyId, $issueId, $category);

        if (! $issue) {
            return $this->notFoundAction();
        }

        return (new ItemModel($issue))
            ->setOption('X-Source', 'Store');
    }

    /**
     * Get issues per assembly.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\IssueProperties[]
     * @query type [string]
     * @query order [string]
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

//        return $this->getListFromDatabase($assemblyId, $types, $kinds, $categories, $orderQuery);
        return $this->getListFromStore($assemblyId, $types, $kinds, $categories, $orderQuery);
    }

    /**
     * Save one issue.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Issue
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

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * Update one issue.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Issue
     */
    public function patch($id, $data)
    {
        $assemblyId = $this->params('id');
        $category = strtoupper($this->params('category', 'a'));
        $issue = $this->issueService->get($id, $assemblyId, $category);

        if (! $issue) {
            return $this->notFoundAction();
        }

        $form = new Form\Issue();
        $form->setObject($issue);
        $form->setData($data);

        if ($form->isValid()) {
            $this->issueService->update($form->getObject());
            return (new EmptyModel())->setStatus(205);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Status[]
     */
    public function progressAction()
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $category = strtoupper($this->params('category', 'a'));

        $collection = $this->issueService->fetchProgress($assemblyId, $issueId, $category);
        $collectionCount = count($collection);

        return (new CollectionModel($collection))
            ->setStatus(206)
            ->setRange(0, $collectionCount, $collectionCount);
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\IssueValue[]
     * @query rod asc|desc
     * @query fjoldi [number]
     */
    public function speechTimesAction()
    {
        $assemblyId = $this->params('id', 0);
        $order = $this->params()->fromQuery('rod', 'desc');
        $size = $this->params()->fromQuery('fjoldi', 5);
        $categories = array_map(function ($category) {
            return strtoupper($category);
        }, explode(',', $this->params('category', 'a,b')));

        $collection = $this->fetchSpeechTimesFromStore(
            $assemblyId,
            $size,
            $order === 'desc' ? -1 : 1,
            $categories
        );

//        $collection = $this->fetchSpeechTimesFromService(
//            $assemblyId,
//            $size,
//            $order,
//            $categories
//        );
        return (new CollectionModel($collection))
            ->setOption('X-Source', 'Store')
            ->setStatus(206)
            ->setRange(0, count($collection), count($collection));
    }

    public function statisticsAction()
    {
        $assemblyId = $this->params('id');

        $object = $this->fetchStatisticsFromStore($assemblyId);
//        $object = $this->fetchStatisticsFromService($assemblyId);

        return (new ItemModel($object))
            ->setOption('X-Source', 'Store');
    }

    /**
     * List options for Assembly collection.
     *
     * @return \Rend\View\Model\ModelInterface
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
     * @param int $assemblyId
     * @param int $issueId
     * @param string $category
     * @return \Althingi\Model\IssueProperties
     * @deprecated
     */
    private function getFromDatabase(int $assemblyId, int $issueId, string $category = 'A')
    {
        $issue = $this->issueService->getWithDate($issueId, $assemblyId, $category);

        if (! $issue) {
            return null;
        }

        $issue->setGoal(Transformer::htmlToMarkdown($issue->getGoal()));
        $issue->setMajorChanges(Transformer::htmlToMarkdown($issue->getMajorChanges()));
        $issue->setChangesInLaw(Transformer::htmlToMarkdown($issue->getChangesInLaw()));
        $issue->setCostsAndRevenues(Transformer::htmlToMarkdown($issue->getCostsAndRevenues()));
        $issue->setAdditionalInformation(Transformer::htmlToMarkdown($issue->getAdditionalInformation()));
        $issue->setDeliveries(Transformer::htmlToMarkdown($issue->getDeliveries()));

//        $assembly = $this->assemblyService->get($assemblyId);
        $proponents = $issue->isA()
            ? $this->congressmanService->fetchProponentsByIssue($assemblyId, $issueId)
            : [];//$issue->getCongressmanId() ? [$this->congressmanService->get($issue->getCongressmanId())] : [];
        $voteDates = $issue->isA()
            ? $this->voteService->fetchDateFrequencyByIssue($assemblyId, $issueId)
            : [];
        $speech = $this->speechService->fetchFrequencyByIssue($assemblyId, $issueId, $category);
        $speakers = $this->congressmanService->fetchAccumulatedTimeByIssue($assemblyId, $issueId, $category);

        $speakersWithParties = array_map(function (Model\CongressmanAndDateRange $congressman) {
            return (new Model\CongressmanPartyProperties())
                ->setCongressman($congressman)
                ->setParty($this->partyService->getByCongressman(
                    $congressman->getCongressmanId(),
                    $congressman->getBegin()
                ))->setConstituency($this->constituencyService->getByCongressman(
                    $congressman->getCongressmanId(),
                    $congressman->getBegin()
                ));
        }, $speakers);

        $issueProperties = (new Model\IssueProperties())
            ->setIssue($issue)
//            ->setVoteRange(DateAndCountSequence::buildDateRange($assembly->getFrom(), $assembly->getTo(), $voteDates))
            ->setVoteRange($voteDates)
//            ->setSpeechRange(DateAndCountSequence::buildDateRange($assembly->getFrom(), $assembly->getTo(), $speech))
            ->setSpeechRange($speech)
            ->setSpeakers($speakersWithParties);

        $proponentsAndParty = $issue->isA()
            ? array_map(function (Model\Proponent $proponent) use ($issue) {
                return (new Model\CongressmanPartyProperties())
                    ->setCongressman($proponent)
                    ->setParty($this->partyService->getByCongressman(
                        $proponent->getCongressmanId(),
                        $issue->getDate()
                    ))->setConstituency($this->constituencyService->getByCongressman(
                        $proponent->getCongressmanId(),
                        $issue->getDate()
                    ));
            }, $proponents)
            : [];
        $issueProperties->setProponents($proponentsAndParty);

        return $issueProperties;
    }

    /**
     * @param int $assemblyId
     * @param int $issueId
     * @param string $category
     * @return \Althingi\Model\IssueProperties
     */
    private function getFromStore(int $assemblyId, int $issueId, string $category = 'A')
    {
        return $this->issueStore->get($assemblyId, $issueId, $category);
    }

    /**
     * @param int $assemblyId
     * @param array $types
     * @param array $kinds
     * @param array $categories
     * @param string|null $orderQuery
     * @return CollectionModel
     * @deprecated
     */
    private function getListFromDatabase(
        int $assemblyId,
        array $types = [],
        array $kinds = [],
        array $categories = [],
        string $orderQuery = null
    ) {

        $count = $this->issueService->countByAssembly($assemblyId, $types, $kinds, $categories);
        $range = $this->getRange($this->getRequest(), $count);
        $issues = $this->issueService->fetchByAssembly(
            $assemblyId,
            $range->getFrom(),
            $range->getSize(),
            $orderQuery,
            $types,
            $kinds,
            $categories ? : ['A']
        );

        $issuesAndProperties = array_map(function (Model\IssueAndDate $issue) use ($assemblyId) {
            $issue->setGoal(Transformer::htmlToMarkdown($issue->getGoal()));
            $issue->setMajorChanges(Transformer::htmlToMarkdown($issue->getMajorChanges()));
            $issue->setChangesInLaw(Transformer::htmlToMarkdown($issue->getChangesInLaw()));
            $issue->setCostsAndRevenues(Transformer::htmlToMarkdown($issue->getCostsAndRevenues()));
            $issue->setAdditionalInformation(Transformer::htmlToMarkdown($issue->getAdditionalInformation()));
            $issue->setDeliveries(Transformer::htmlToMarkdown($issue->getDeliveries()));

            $issueAndProperty = (new Model\IssueProperties())
                ->setIssue($issue);

            $proponents = $issue->isA()
                ? $this->congressmanService->fetchProponentsByIssue($assemblyId, $issue->getIssueId())
                : [];
            $proponentsAndParty = array_map(function (Model\Proponent $proponent) use ($issue) {
                return (new Model\CongressmanPartyProperties())
                    ->setCongressman($proponent)
                    ->setParty(
                        $this->partyService->getByCongressman($proponent->getCongressmanId(), $issue->getDate())
                    )->setConstituency(
                        $this->constituencyService->getByCongressman($proponent->getCongressmanId(), $issue->getDate())
                    );
            }, $proponents);
            $issueAndProperty->setProponents($proponentsAndParty);

            return $issueAndProperty;
        }, $issues);

        return (new CollectionModel($issuesAndProperties))
            ->setStatus(206)
            ->setRange($range->getFrom(), $range->getFrom() + count($issuesAndProperties), $count);
    }

    /**
     * @param int $assemblyId
     * @param array $types
     * @param array $kinds
     * @param array $categories
     * @param string|null $orderQuery
     * @return ModelInterface
     */
    private function getListFromStore(
        int $assemblyId,
        array $types = [],
        array $kinds = [],
        array $categories = [],
        string $orderQuery = null
    ) {

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
            ->setRange($range->getFrom(), $range->getFrom() + count($issues), $count)
            ->setOption('X-Source', 'Store');
    }

    /**
     * Set service.
     *
     * @param \Althingi\Service\Congressman $congressman
     * @return $this;
     */
    public function setCongressmanService(Service\Congressman $congressman)
    {
        $this->congressmanService = $congressman;
        return $this;
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
     * Set service.
     *
     * @param \Althingi\Service\Party $party
     * @return $this
     */
    public function setPartyService(Service\Party $party)
    {
        $this->partyService = $party;
        return $this;
    }

    /**
     * @param \Althingi\Service\Document $document
     * @return $this
     */
    public function setDocumentService(Service\Document $document)
    {
        $this->documentService = $document;
        return $this;
    }

    /**
     * @param \Althingi\Service\Vote $vote
     * @return $this
     */
    public function setVoteService(Service\Vote $vote)
    {
        $this->voteService = $vote;
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
     * @param \Althingi\Service\Speech $speech
     * @return $this
     */
    public function setSpeechService(Service\Speech $speech)
    {
        $this->speechService = $speech;
        return $this;
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
     * @param Constituency $constituency
     * @return $this
     */
    public function setConstituencyService(Constituency $constituency)
    {
        $this->constituencyService = $constituency;
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

    /**
     * @param int $assemblyId
     * @return Model\IssuesStatusProperties
     * @deprecated
     */
    private function fetchStatisticsFromService(int $assemblyId): Model\IssuesStatusProperties
    {
        $assembly = $this->assemblyService->get($assemblyId);
        return (new Model\IssuesStatusProperties())
            ->setBills($this->issueService->fetchNonGovernmentBillStatisticsByAssembly($assembly->getAssemblyId()))
            ->setGovernmentBills(
                $this->issueService->fetchGovernmentBillStatisticsByAssembly($assembly->getAssemblyId())
            )
            ->setTypes($this->issueService->fetchCountByCategory($assembly->getAssemblyId()))
            ->setCategories($this->categoryService->fetchByAssembly($assembly->getAssemblyId())) //@todo remove this
        ;
    }

    /**
     * @param int $assemblyId
     * @return Model\IssuesStatusProperties
     */
    private function fetchStatisticsFromStore(int $assemblyId): Model\IssuesStatusProperties
    {
        return (new Model\IssuesStatusProperties())
            ->setBills($this->issueStore->fetchNonGovernmentBillStatisticsByAssembly($assemblyId))
            ->setGovernmentBills($this->issueStore->fetchGovernmentBillStatisticsByAssembly($assemblyId))
            ->setProposals($this->issueStore->fetchProposalStatisticsByAssembly($assemblyId))
            ->setTypes($this->issueStore->fetchCountByCategory($assemblyId))
            ->setCategories($this->categoryStore->fetchByAssembly($assemblyId))
            ;
    }

    /**
     * @param int $assemblyId
     * @param int $size
     * @param string $order
     * @param array $categories
     * @return array
     * @deprecated
     */
    private function fetchSpeechTimesFromService(
        int $assemblyId,
        int $size,
        string $order,
        array $categories = ['A', 'B']
    ) {
        $assembly = $this->assemblyService->get($assemblyId);
        $collection = $this->issueService->fetchByAssemblyAndSpeechTime(
            $assemblyId,
            $size,
            $order,
            $categories
        );

        return array_map(function (Model\IssueValue $issue) use ($assembly) {
            $issue->setGoal(Transformer::htmlToMarkdown($issue->getGoal()));
            $issue->setMajorChanges(Transformer::htmlToMarkdown($issue->getMajorChanges()));
            $issue->setChangesInLaw(Transformer::htmlToMarkdown($issue->getChangesInLaw()));
            $issue->setCostsAndRevenues(Transformer::htmlToMarkdown($issue->getCostsAndRevenues()));
            $issue->setAdditionalInformation(Transformer::htmlToMarkdown($issue->getAdditionalInformation()));
            $issue->setDeliveries(Transformer::htmlToMarkdown($issue->getDeliveries()));

            $issueAndProperty = (new Model\IssueProperties())
                ->setIssue($issue);

            $proponents = $this->congressmanService->fetchProponentsByIssue(
                $assembly->getAssemblyId(),
                $issue->getIssueId()
            );
            $proponentsAndParty = $issue->isA()
                ? array_map(function (Model\Proponent $proponent) use ($issue, $assembly) {
                    return (new Model\CongressmanPartyProperties())
                        ->setCongressman($proponent)
                        ->setParty(
                            $this->partyService->getByCongressman($proponent->getCongressmanId(), $assembly->getFrom())
                        );
                }, $proponents)
                : [];
            $issueAndProperty->setProponents($proponentsAndParty);

            return $issueAndProperty;
        }, $collection);
    }

    /**
     * @param int $assemblyId
     * @param int $size
     * @param int $order
     * @param array $categories
     * @return array
     */
    private function fetchSpeechTimesFromStore(
        int $assemblyId,
        int $size,
        int $order,
        array $categories = ['A', 'B']
    ): array {
        return $this->issueStore->fetchByAssemblyAndSpeechTime(
            $assemblyId,
            $size,
            $order === 'desc' ? -1 : 1,
            $categories
        );
    }
}
