<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Issue as IssueForm;
use Althingi\Lib\ServiceAssemblyAwareInterface;
use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Lib\ServiceDocumentAwareInterface;
use Althingi\Lib\ServiceIssueAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Lib\ServiceSpeechAwareInterface;
use Althingi\Lib\ServiceVoteAwareInterface;
use Althingi\Service\Assembly;
use Althingi\Service\Congressman;
use Althingi\Service\Document;
use Althingi\Service\Party;
use Althingi\Service\Issue;
use Althingi\Service\Speech;
use Althingi\Service\Vote;
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

    /** @var  \Althingi\Service\Assembly */
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

        $issue = $this->issueService->get($issueId, $assemblyId);

        if (!$issue) {
            return $this->notFoundAction();
        }

        $assembly = $this->assemblyService->get($assemblyId);

        $issue->proponent = $this->congressmanService->get($issue->congressman_id);
        if ($issue->proponent) {
            $issue->proponent->party = $this->partyService->getByCongressman(
                $issue->congressman_id,
                new \DateTime($issue->date)
            );
        }

        $dates = $this->voteService->fetchDateFrequencyByIssue($assemblyId, $issueId);
        $issue->voteRange = $this->buildDateRange(
            new DateTime($assembly->from),
            new DateTime($assembly->to),
            $dates
        );

        $speech = $this->speechService->fetchFrequencyByIssue($assemblyId, $issueId);
        $issue->speechRange = $this->buildDateRange(
            new DateTime($assembly->from),
            new DateTime($assembly->to),
            $speech
        );

        $issue->speekers = $this->congressmanService->fetchAccumulatedTimeByIssue($assemblyId, $issueId);
        array_walk($issue->speekers, function ($congressman) {
            $congressman->party = $this->partyService->getByCongressman(
                $congressman->congressman_id,
                new DateTime($congressman->begin)
            );
        });

        return (new ItemModel($issue))
            ->setOption('Access-Control-Allow-Origin', '*');
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

        $congressmanService = $this->congressmanService;
        $partyService = $this->partyService;

        $count = $this->issueService->countByAssembly($assemblyId, $types);
        $range = $this->getRange($this->getRequest(), $count);
        $issues = $this->issueService->fetchByAssembly(
            $assemblyId,
            $range['from'],
            ($range['to']-$range['from']),
            $orderQuery,
            $types
        );

        array_walk($issues, function ($issue) use ($congressmanService, $partyService, $assemblyId) {
            if ($issue->congressman_id) {
                $issue->congressman = $congressmanService->get($issue->congressman_id);
                $issue->congressman->party = $partyService->getByCongressman(
                    $issue->congressman_id,
                    new DateTime($issue->date)
                );
            }
        });

        return (new CollectionModel($issues))
            ->setStatus(206)
            ->setOption('Access-Control-Allow-Origin', '*')
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
        $issueService = $this->getServiceLocator()
            ->get('Althingi\Service\Issue');

        $form = (new IssueForm())
            ->setData(array_merge(
                $data,
                ['assembly_id' => $this->params('id'), 'issue_id' => $id]
            ));

        if ($form->isValid()) {
            $issueService->create($form->getObject());
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
        $assemblyId = $this->params('id', 0);
        $issueService = $this->getServiceLocator()->get('Althingi\Service\Issue');
        $issue = $issueService->get($id, $assemblyId);

        if (!$issue) {
            return $this->notFoundAction();
        }

        $form = new IssueForm();
        $form->setObject($issue);
        $form->setData($data);

        if ($form->isValid()) {
            $issueService->update($form->getObject());
            return (new EmptyModel())->setStatus(200);
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

    private function buildDateRange(\DateTime $begin, \DateTime $end, $range)
    {
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
