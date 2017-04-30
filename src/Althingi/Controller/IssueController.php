<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Issue as IssueForm;
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
use Althingi\Model\IssueProperties;
use Althingi\Service\Assembly;
use Althingi\Service\Congressman;
use Althingi\Service\Document;
use Althingi\Service\Party;
use Althingi\Service\Issue;
use Althingi\Service\Speech;
use Althingi\Service\Vote;
use Althingi\Lib\Transformer;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use DateTime;

class IssueController extends AbstractRestfulController implements
    ServiceCongressmanAwareInterface,
    ServiceIssueAwareInterface,
    ServicePartyAwareInterface,
    ServiceDocumentAwareInterface,
    ServiceVoteAwareInterface,
    ServiceAssemblyAwareInterface,
    ServiceSpeechAwareInterface
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

    /** @var  \Althingi\Service\President */
    private $assemblyService;

    /** @var  \Althingi\Service\Speech */
    private $speechService;

    /**
     * Get one issie.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     */
    public function get($id)
    {
        $assemblyId = $this->params('id', 0);
        $issueId = $this->params('issue_id', 0);

        $issue = $this->issueService->getWithDate($issueId, $assemblyId);

        if (!$issue) {
            return $this->notFoundAction();
        }

        $assembly = $this->assemblyService->get($assemblyId);
        $proponent = $this->congressmanService->get($issue->getCongressmanId());
        $voteDates = $this->voteService->fetchDateFrequencyByIssue($assemblyId, $issueId);
        $speech = $this->speechService->fetchFrequencyByIssue($assemblyId, $issueId);
        $speakers = $this->congressmanService->fetchAccumulatedTimeByIssue($assemblyId, $issueId);

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
            ->setVoteRange($this->buildDateRange($assembly->getFrom(), $assembly->getTo(), $voteDates))
            ->setSpeechRange($this->buildDateRange($assembly->getFrom(), $assembly->getTo(), $speech))
            ->setSpeakers($speakersWithParties);

        if ($proponent) {
            $congressmanAndParty = new CongressmanPartyProperties();
            $congressmanAndParty->setCongressman($proponent);
            $congressmanAndParty->setParty(
                $this->partyService->getByCongressman($issue->getCongressmanId(), $issue->getDate())
            );
            $issueProperties->setProponent($congressmanAndParty);
        }

        return (new ItemModel($issueProperties));
    }

    /**
     * Get issues per assembly.
     *
     * @return \Rend\View\Model\ModelInterface
     * @attr id assembly ID
     */
    public function getList()
    {
        $assemblyId = $this->params('id', null);
        $typeQuery = $this->params()->fromQuery('type', null);
        $orderQuery = $this->params()->fromQuery('order', null);
        $types = $typeQuery ? str_split($typeQuery) : [];

        $count = $this->issueService->countByAssembly($assemblyId, $types);
        $range = $this->getRange($this->getRequest(), $count);
        $issues = $this->issueService->fetchByAssembly(
            $assemblyId,
            $range['from'],
            ($range['to']-$range['from']),
            $orderQuery,
            $types
        );

        $issuesAndProperties = array_map(function (IssueAndDateModel $issue) use ($assemblyId) {
            $issue->setGoal(Transformer::htmlToMarkdown($issue->getGoal()));
            $issue->setMajorChanges(Transformer::htmlToMarkdown($issue->getMajorChanges()));
            $issue->setChangesInLaw(Transformer::htmlToMarkdown($issue->getChangesInLaw()));
            $issue->setCostsAndRevenues(Transformer::htmlToMarkdown($issue->getCostsAndRevenues()));
            $issue->setAdditionalInformation(Transformer::htmlToMarkdown($issue->getAdditionalInformation()));
            $issue->setDeliveries(Transformer::htmlToMarkdown($issue->getDeliveries()));

            $issueAndProperty = (new IssueProperties())
                ->setIssue($issue);

            if ($issue->getCongressmanId()) {
                $congressman = $this->congressmanService->get($issue->getCongressmanId());
                $congressmanAndParty = (new CongressmanPartyProperties())
                    ->setCongressman($congressman)
                    ->setParty($this->partyService->getByCongressman($issue->getCongressmanId(), $issue->getDate()));

                $issueAndProperty->setProponent($congressmanAndParty);
            }
        }, $issues);

        return (new CollectionModel($issuesAndProperties))
            ->setStatus(206)
            ->setOption('Access-Control-Expose-Headers', 'Range, Range-Unit, Content-Range') //TODO should go into Rend
            ->setRange($range['from'], $range['to'], $count);
    }

    /**
     * Save one issue.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $id;

        $form = (new IssueForm())
            ->setData(array_merge($data, ['assembly_id' => $assemblyId, 'issue_id' => $issueId]));

        if ($form->isValid()) {
            $this->issueService->create($form->getObject());
            return (new EmptyModel())->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * Update one issue.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function patch($id, $data)
    {
        $assemblyId = $this->params('id');
        $issue = $this->issueService->get($id, $assemblyId);

        if (!$issue) {
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
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
    }

    /**
     * Set service.
     *
     * @param Issue $issue
     */
    public function setIssueService(Issue $issue)
    {
        $this->issueService = $issue;
    }

    /**
     * Set service.
     *
     * @param Party $party
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
    }

    /**
     * @param Document $document
     */
    public function setDocumentService(Document $document)
    {
        $this->documentService = $document;
    }

    /**
     * @param Vote $vote
     */
    public function setVoteService(Vote $vote)
    {
        $this->voteService = $vote;
    }

    /**
     * @param Assembly $assembly
     */
    public function setAssemblyService(Assembly $assembly)
    {
        $this->assemblyService = $assembly;
    }

    /**
     * @param Speech $speech
     */
    public function setSpeechService(Speech $speech)
    {
        $this->speechService = $speech;
    }

    private function buildDateRange(\DateTime $begin, \DateTime $end = null, $range = 0)
    {
        $end = $end ? : new DateTime();
        $interval = new \DateInterval('P1M');
        $dateRange = new \DatePeriod($begin, $interval, $end);

        return array_map(function ($dateObject) use ($range) {
            $date = $dateObject->format('Y-m');
            $count = array_filter($range, function ($item) use ($date) {
                return $item->year_month == $date;
            });
            return count($count) >= 1
                ? array_pop($count)
                : (object) [
                    'count' => 0,
                    'year_month' => $date
                ];
        }, iterator_to_array($dateRange));
    }
}
