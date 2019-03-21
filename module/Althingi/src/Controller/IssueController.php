<?php

namespace Althingi\Controller;

use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use Althingi\Lib\DateAndCountSequence;
use Althingi\Lib\ServiceSearchIssueAwareInterface;
use Althingi\Lib\StoreIssueAwareInterface;
use Althingi\Lib\ServiceAssemblyAwareInterface;
use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Lib\ServiceDocumentAwareInterface;
use Althingi\Lib\ServiceIssueAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Lib\ServiceSpeechAwareInterface;
use Althingi\Lib\ServiceVoteAwareInterface;
use Althingi\Lib\Transformer;
use Althingi\Utils\CategoryParam;
use Althingi\Form;
use Althingi\Model;
use Althingi\Service;
use Althingi\Store;

class IssueController extends AbstractRestfulController implements
    ServiceCongressmanAwareInterface,
    ServiceIssueAwareInterface,
    ServicePartyAwareInterface,
    ServiceDocumentAwareInterface,
    ServiceVoteAwareInterface,
    ServiceAssemblyAwareInterface,
    ServiceSpeechAwareInterface,
    ServiceSearchIssueAwareInterface,
    StoreIssueAwareInterface
{
    use Range;
    use CategoryParam;

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

    /** @var  \Althingi\Store\Issue */
    private $issueStore;

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
        $category = $this->getCategoryFromQuery();

        return $this->getFromDatabase($assemblyId, $issueId, $category);
//        return $this->getFromStore($assemblyId, $issueId, $category);
    }

    /**
     * Get issues per assembly.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\IssueProperties[]
     * @query leit [string]
     * @query type [string]
     * @query category [string]
     * @query order [string]
     * @query category
     */
    public function getList()
    {
        $assemblyId = $this->params('id', null);
        $typeQuery = $this->params()->fromQuery('type', null);
        $kindQuery = $this->params()->fromQuery('kind', null);
        $orderQuery = $this->params()->fromQuery('order', null);
        $types = $typeQuery ? str_split($typeQuery) : [];
        $kinds = $kindQuery ? explode(',', $kindQuery) : [];
        $categories = $this->getCategoriesFromQuery();

        return $this->getListFromDatabase($assemblyId, $types, $kinds, $categories, $orderQuery);
//        return $this->getListFromStore($assemblyId, $types, $kinds, $categories, $orderQuery);
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
        $issueId = $id;

        $form = (new Form\Issue())
            ->setData(array_merge($data, ['assembly_id' => $assemblyId, 'issue_id' => $issueId]));

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
        $issue = $this->issueService->get($id, $assemblyId, 'A');

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

        $collection = $this->issueService->fetchProgress($assemblyId, $issueId);
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
     * @query category
     */
    public function speechTimesAction()
    {
        $assemblyId = $this->params('id', 0);
        $assembly = $this->assemblyService->get($assemblyId);
        $order = $this->params()->fromQuery('rod', null);
        $size = $this->params()->fromQuery('fjoldi', null);
        $category = $this->getCategoriesFromQuery();
        $collection = $this->issueService->fetchByAssemblyAndSpeechTime(
            $assemblyId,
            $size,
            $order,
            $category ? : ['A']
        );

        $issuesAndProperties = array_map(function (Model\IssueValue $issue) use ($assembly) {
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
        $issuesAndPropertiesCount = count($issuesAndProperties);

        return (new CollectionModel($issuesAndProperties))
            ->setStatus(206)
            ->setRange(0, $issuesAndPropertiesCount, $issuesAndPropertiesCount);
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
     * @return \Rend\View\Model\ModelInterface
     * @deprecated
     */
    private function getFromDatabase(int $assemblyId, int $issueId, string $category = 'A')
    {
        $issue = $this->issueService->getWithDate($issueId, $assemblyId, $category);

        if (! $issue) {
            return $this->notFoundAction();
        }

        $issue->setGoal(Transformer::htmlToMarkdown($issue->getGoal()));
        $issue->setMajorChanges(Transformer::htmlToMarkdown($issue->getMajorChanges()));
        $issue->setChangesInLaw(Transformer::htmlToMarkdown($issue->getChangesInLaw()));
        $issue->setCostsAndRevenues(Transformer::htmlToMarkdown($issue->getCostsAndRevenues()));
        $issue->setAdditionalInformation(Transformer::htmlToMarkdown($issue->getAdditionalInformation()));
        $issue->setDeliveries(Transformer::htmlToMarkdown($issue->getDeliveries()));

        $assembly = $this->assemblyService->get($assemblyId);
//        $proponent = $issue->getCongressmanId() ? $this->congressmanService->get($issue->getCongressmanId()) : null;
        $proponents = $this->congressmanService->fetchProponentsByIssue($assemblyId, $issueId);
        $voteDates = $this->voteService->fetchDateFrequencyByIssue($assemblyId, $issueId);
        $speech = $this->speechService->fetchFrequencyByIssue($assemblyId, $issueId, $category);
        $speakers = $this->congressmanService->fetchAccumulatedTimeByIssue($assemblyId, $issueId, $category);

        $speakersWithParties = array_map(function (Model\CongressmanAndDateRange $congressman) {
            return (new Model\CongressmanPartyProperties())
                ->setCongressman($congressman)
                ->setParty($this->partyService->getByCongressman(
                    $congressman->getCongressmanId(),
                    $congressman->getBegin()
                ));
        }, $speakers);

        $issueProperties = (new Model\IssueProperties())
            ->setIssue($issue)
            ->setVoteRange(DateAndCountSequence::buildDateRange($assembly->getFrom(), $assembly->getTo(), $voteDates))
            ->setSpeechRange(DateAndCountSequence::buildDateRange($assembly->getFrom(), $assembly->getTo(), $speech))
            ->setSpeakers($speakersWithParties);

        $proponentsAndParty = $issue->isA()
            ? array_map(function (Model\Proponent $proponent) use ($issue) {
                return (new Model\CongressmanPartyProperties())
                    ->setCongressman($proponent)
                    ->setParty(
                        $this->partyService->getByCongressman($proponent->getCongressmanId(), $issue->getDate())
                    );
            }, $proponents)
            : [];
        $issueProperties->setProponents($proponentsAndParty);

        return (new ItemModel($issueProperties));
    }

    /**
     * @param int $assemblyId
     * @param int $issueId
     * @param string $category
     * @return \Rend\View\Model\ModelInterface
     */
    private function getFromStore(int $assemblyId, int $issueId, string $category = 'A')
    {
        $issueProperties = $this->issueStore->get($assemblyId, $issueId, $category);

        if (! $issueProperties) {
            return $this->notFoundAction();
        }

        $assembly = $this->assemblyService->get($assemblyId);
        $voteDates = $this->voteService->fetchDateFrequencyByIssue($assemblyId, $issueId);
        $speech = $this->speechService->fetchFrequencyByIssue($assemblyId, $issueId, $category);
        $speakers = $this->congressmanService->fetchAccumulatedTimeByIssue($assemblyId, $issueId, $category);
        $speakersWithParties = array_map(function (Model\CongressmanAndDateRange $congressman) use ($assembly) {
            return (new Model\CongressmanPartyProperties())
                ->setCongressman($congressman)
                ->setParty($this->partyService->getByCongressman(
                    $congressman->getCongressmanId(),
                    $congressman->getBegin()
                ))
                ->setAssembly($assembly);
        }, $speakers);

//        $issueProperties
//            ->setVoteRange(DateAndCountSequence::buildDateRange($assembly->getFrom(), $assembly->getTo(), $voteDates))
//            ->setSpeechRange(DateAndCountSequence::buildDateRange($assembly->getFrom(), $assembly->getTo(), $speech))
//            ->setSpeakers($speakersWithParties);

        return (new ItemModel($issueProperties));
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
    private function getListFromDatabase(int $assemblyId, array $types = [], array $kinds = [], array $categories = [], string $orderQuery = null)
    {
        $query = $this->params()->fromQuery('leit', null);

        if ($query) {
            $issues = $this->issueSearchService->fetchByAssembly($query, $assemblyId);
            $count = count($issues);
            $range = $this->getRange($this->getRequest(), $count);

            $issues = array_map(function (Model\IssueAndDate $issue) {
                $documents = $this->documentService->fetchByIssue($issue->getAssemblyId(), $issue->getIssueId());
                $issue->setDate($documents[0]->getDate());
                return $issue;
            }, $issues);
        } else {
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
        }

        $issuesAndProperties = array_map(function (Model\IssueAndDate $issue) use ($assemblyId) {
            $issue->setGoal(Transformer::htmlToMarkdown($issue->getGoal()));
            $issue->setMajorChanges(Transformer::htmlToMarkdown($issue->getMajorChanges()));
            $issue->setChangesInLaw(Transformer::htmlToMarkdown($issue->getChangesInLaw()));
            $issue->setCostsAndRevenues(Transformer::htmlToMarkdown($issue->getCostsAndRevenues()));
            $issue->setAdditionalInformation(Transformer::htmlToMarkdown($issue->getAdditionalInformation()));
            $issue->setDeliveries(Transformer::htmlToMarkdown($issue->getDeliveries()));

            $issueAndProperty = (new Model\IssueProperties())
                ->setIssue($issue);

            $proponents = $issue->getCategory() === 'A'
                ? $this->congressmanService->fetchProponentsByIssue($assemblyId, $issue->getIssueId())
                : [];
            $proponentsAndParty = array_map(function (Model\Proponent $proponent) use ($issue) {
                return (new Model\CongressmanPartyProperties())
                    ->setCongressman($proponent)
                    ->setParty(
                        $this->partyService->getByCongressman($proponent->getCongressmanId(), $issue->getDate())
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
     * @return CollectionModel
     */
    private function getListFromStore(int $assemblyId, array $types = [], array $kinds = [], array $categories = [], string $orderQuery = null)
    {

        $issues = $this->issueStore->fetchByAssembly($assemblyId, $types, $kinds, $categories);
        $count = $this->issueStore->countByAssembly($assemblyId, $types, $kinds, $categories);
        $range = $this->getRange($this->getRequest(), $count);

        return (new CollectionModel($issues))
            ->setStatus(206)
            ->setRange($range->getFrom(), $range->getFrom() + count($issues), $count);
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
}
