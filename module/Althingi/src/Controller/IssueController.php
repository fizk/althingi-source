<?php

namespace Althingi\Controller;

use Althingi\Form\Issue as IssueForm;
use Althingi\Lib\DateAndCountSequence;
use Althingi\Lib\ServiceSearchIssueAwareInterface;
use Althingi\Model\CongressmanAndDateRange;
use Althingi\Model\IssueAndDate as IssueAndDateModel;
use Althingi\Lib\ServiceAssemblyAwareInterface;
use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Lib\ServiceDocumentAwareInterface;
use Althingi\Lib\ServiceIssueAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Lib\ServiceSpeechAwareInterface;
use Althingi\Lib\ServiceVoteAwareInterface;
use Althingi\Model\CongressmanPartyProperties;
use Althingi\Model\IssueAndDate;
use Althingi\Model\IssueProperties;
use Althingi\Model\IssueValue;
use Althingi\Model\Proponent;
use Althingi\Service\Assembly;
use Althingi\Service\Congressman;
use Althingi\Service\Document;
use Althingi\Service\Party;
use Althingi\Service\Issue;
use Althingi\Service\SearchIssue;
use Althingi\Service\Speech;
use Althingi\Service\Vote;
use Althingi\Lib\Transformer;
use Althingi\Utils\CategoryParam;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;

class IssueController extends AbstractRestfulController implements
    ServiceCongressmanAwareInterface,
    ServiceIssueAwareInterface,
    ServicePartyAwareInterface,
    ServiceDocumentAwareInterface,
    ServiceVoteAwareInterface,
    ServiceAssemblyAwareInterface,
    ServiceSpeechAwareInterface,
    ServiceSearchIssueAwareInterface
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

    /** @var  \Althingi\Service\President */
    private $assemblyService;

    /** @var  \Althingi\Service\Speech */
    private $speechService;

    /** @var  \Althingi\Service\SearchIssue */
    private $issueSearchService;

    /**
     * Get one issie.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\IssueProperties
     */
    public function get($id)
    {
        $assemblyId = $this->params('id', 0);
        $issueId = $this->params('issue_id', 0);
        $category = $this->getCategoryFromQuery();

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

        $speakersWithParties = array_map(function (CongressmanAndDateRange $congressman) {
            return (new CongressmanPartyProperties())
                ->setCongressman($congressman)
                ->setParty($this->partyService->getByCongressman(
                    $congressman->getCongressmanId(),
                    $congressman->getBegin()
                ));
        }, $speakers);

        $issueProperties = (new IssueProperties())
            ->setIssue($issue)
            ->setVoteRange(DateAndCountSequence::buildDateRange($assembly->getFrom(), $assembly->getTo(), $voteDates))
            ->setSpeechRange(DateAndCountSequence::buildDateRange($assembly->getFrom(), $assembly->getTo(), $speech))
            ->setSpeakers($speakersWithParties);

        $proponentsAndParty = $issue->isA()
            ? array_map(function (Proponent $proponent) use ($issue) {
                return (new CongressmanPartyProperties())
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
     * Get issues per assembly.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\IssueProperties[]
     * @query leit [string]
     * @query type [string]
     * @query category [string]
     * @query order [string]
     */
    public function getList()
    {
        $query = $this->params()->fromQuery('leit', null);
        $assemblyId = $this->params('id', null);
        $typeQuery = $this->params()->fromQuery('type', null);
        $kindQuery = $this->params()->fromQuery('kind', null);
        $orderQuery = $this->params()->fromQuery('order', null);
        $types = $typeQuery ? str_split($typeQuery) : [];
        $kinds = explode(',', $kindQuery);
        $categories = $this->getCategoriesFromQuery();

        if ($query) {
            $issues = $this->issueSearchService->fetchByAssembly($query, $assemblyId);
            $count = count($issues);
            $range = $this->getRange($this->getRequest(), $count);

            $issues = array_map(function (IssueAndDate $issue) {
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

        $issuesAndProperties = array_map(function (IssueAndDateModel $issue) use ($assemblyId) {
            $issue->setGoal(Transformer::htmlToMarkdown($issue->getGoal()));
            $issue->setMajorChanges(Transformer::htmlToMarkdown($issue->getMajorChanges()));
            $issue->setChangesInLaw(Transformer::htmlToMarkdown($issue->getChangesInLaw()));
            $issue->setCostsAndRevenues(Transformer::htmlToMarkdown($issue->getCostsAndRevenues()));
            $issue->setAdditionalInformation(Transformer::htmlToMarkdown($issue->getAdditionalInformation()));
            $issue->setDeliveries(Transformer::htmlToMarkdown($issue->getDeliveries()));

            $issueAndProperty = (new IssueProperties())
                ->setIssue($issue);

            $proponents = $issue->getCategory() === 'A'
                ? $this->congressmanService->fetchProponentsByIssue($assemblyId, $issue->getIssueId())
                : [];
            $proponentsAndParty = array_map(function (Proponent $proponent) use ($issue) {
                return (new CongressmanPartyProperties())
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

        $form = (new IssueForm())
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

        $form = new IssueForm();
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

        $issuesAndProperties = array_map(function (IssueValue $issue) use ($assembly) {
            $issue->setGoal(Transformer::htmlToMarkdown($issue->getGoal()));
            $issue->setMajorChanges(Transformer::htmlToMarkdown($issue->getMajorChanges()));
            $issue->setChangesInLaw(Transformer::htmlToMarkdown($issue->getChangesInLaw()));
            $issue->setCostsAndRevenues(Transformer::htmlToMarkdown($issue->getCostsAndRevenues()));
            $issue->setAdditionalInformation(Transformer::htmlToMarkdown($issue->getAdditionalInformation()));
            $issue->setDeliveries(Transformer::htmlToMarkdown($issue->getDeliveries()));

            $issueAndProperty = (new IssueProperties())
                ->setIssue($issue);

            $proponents = $this->congressmanService->fetchProponentsByIssue(
                $assembly->getAssemblyId(),
                $issue->getIssueId()
            );
            $proponentsAndParty = $issue->isA()
                ? array_map(function (Proponent $proponent) use ($issue, $assembly) {
                    return (new CongressmanPartyProperties())
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
     * Set service.
     *
     * @param Congressman $congressman
     * @return $this;
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
        return $this;
    }

    /**
     * Set service.
     *
     * @param Issue $issue
     * @return $this;
     */
    public function setIssueService(Issue $issue)
    {
        $this->issueService = $issue;
        return $this;
    }

    /**
     * Set service.
     *
     * @param Party $party
     * @return $this
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
        return $this;
    }

    /**
     * @param Document $document
     * @return $this
     */
    public function setDocumentService(Document $document)
    {
        $this->documentService = $document;
        return $this;
    }

    /**
     * @param Vote $vote
     * @return $this
     */
    public function setVoteService(Vote $vote)
    {
        $this->voteService = $vote;
        return $this;
    }

    /**
     * @param Assembly $assembly
     * @return $this
     */
    public function setAssemblyService(Assembly $assembly)
    {
        $this->assemblyService = $assembly;
        return $this;
    }

    /**
     * @param Speech $speech
     * @return $this
     */
    public function setSpeechService(Speech $speech)
    {
        $this->speechService = $speech;
        return $this;
    }

    /**
     * @param \Althingi\Service\SearchIssue $issue
     * @return $this
     */
    public function setSearchIssueService(SearchIssue $issue)
    {
        $this->issueSearchService = $issue;
        return $this;
    }
}
