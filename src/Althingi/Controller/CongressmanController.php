<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 8/06/15
 * Time: 9:05 PM
 */

namespace Althingi\Controller;

use Althingi\Form\Congressman as CongressmanForm;
use Althingi\Lib\ServiceCongressmanAwareInterface;
use Althingi\Lib\ServiceIssueAwareInterface;
use Althingi\Lib\ServiceIssueCategoryAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Lib\ServiceSessionAwareInterface;
use Althingi\Lib\ServiceSpeechAwareInterface;
use Althingi\Lib\ServiceVoteAwareInterface;
use Althingi\Lib\ServiceVoteItemAwareInterface;
use Althingi\Service\Congressman;
use Althingi\Service\Issue;
use Althingi\Service\IssueCategory;
use Althingi\Service\Party;
use Althingi\Service\Session;
use Althingi\Service\Speech;
use Althingi\Service\Vote;
use Althingi\Service\VoteItem;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;

class CongressmanController extends AbstractRestfulController implements
    ServiceCongressmanAwareInterface,
    ServicePartyAwareInterface,
    ServiceSessionAwareInterface,
    ServiceVoteAwareInterface,
    ServiceVoteItemAwareInterface,
    ServiceIssueAwareInterface,
    ServiceSpeechAwareInterface,
    ServiceIssueCategoryAwareInterface
{
    use Range;

    /** @var \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var \Althingi\Service\Party */
    private $partyService;

    /** @var \Althingi\Service\Session */
    private $sessionService;

    /** @var \Althingi\Service\Vote */
    private $voteService;

    /** @var \Althingi\Service\VoteItem */
    private $voteItemService;

    /** @var \Althingi\Service\Issue */
    private $issueService;

    /** @var \Althingi\Service\Speech */
    private $speechService;

    /** @var \Althingi\Service\IssueCategory */
    private $issueCategoryService;

    /**
     * Get one congressman.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     */
    public function get($id)
    {
        if ($congressman = $this->congressmanService->get($id)) {
            $congressman->parties = $this->partyService->fetchByCongressman($id);

            return (new ItemModel($congressman))
                ->setStatus(200)
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return $this->notFoundAction();
    }

    /**
     * Return list of congressmen.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function getList()
    {
        $count = $this->congressmanService->count();
        $range = $this->getRange($this->getRequest(), $count);
        $congressmen = $this->congressmanService->fetchAll($range['from'], $range['to']);

        return (new CollectionModel($congressmen))
            ->setStatus(206)
            ->setRange($range['from'], $range['to'], $count)
            ->setOption('Access-Control-Expose-Headers', 'Range, Range-Unit, Content-Range') //TODO should go into Rend
            ->setOption('Access-Control-Allow-Origin', '*');
    }

    /**
     * Create on congressman entry.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function put($id, $data)
    {
        $form = new CongressmanForm();
        $form->setData(array_merge($data, ['congressman_id' => $id]));

        if ($form->isValid()) {
            $this->congressmanService->create($form->getObject());
            return (new EmptyModel())
                ->setStatus(201)
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return (new ErrorModel($form))
            ->setStatus(400)
            ->setOption('Access-Control-Allow-Origin', '*');
    }

    /**
     * Update congressman.
     *
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function patch($id, $data)
    {
        if (($congressman = $this->congressmanService->get($id)) != null) {
            $form = (new CongressmanForm())
                ->bind($congressman)
                ->setData($data);

            if ($form->isValid()) {
                $this->congressmanService->update($form->getObject());
                return (new EmptyModel())
                    ->setStatus(205)
                    ->setOption('Access-Control-Allow-Origin', '*');
            }

            return (new ErrorModel($form))
                ->setStatus(400)
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return $this->notFoundAction();
    }

    /**
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     */
    public function delete($id)
    {
        if (($congressman = $this->congressmanService->get($id))) {
            $this->congressmanService->delete($id);

            return (new EmptyModel())
                ->setStatus(204)
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return $this->notFoundAction();
    }

    /**
     * Get all members of assembly.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function assemblyAction()
    {
        $assemblyId = $this->params('id');
        $typeArray = [
            'thingmadur' => Congressman::CONGRESSMAN_TYPE_MP,
            'varamadur' => Congressman::CONGRESSMAN_TYPE_SUBSTITUTE
        ];
        $typeQuery = $this->params()->fromQuery('tegund', null);
        $typeParam = array_key_exists($typeQuery, $typeArray) ? $typeArray[$typeQuery] : null;

        $congressmen = array_map(function ($congressman) {
            $congressman->party = $this->partyService->get($congressman->party_id);
            return $congressman;
        }, $this->congressmanService->fetchByAssembly($assemblyId, $typeParam));

        return (new CollectionModel($congressmen))
            ->setStatus(200)
            ->setOption('Access-Control-Allow-Origin', '*');

    }

    public function assemblySummaryAction()
    {
        $assemblyId = $this->params('id');
        $congressmanId = $this->params('congressman_id');
        $fromString = $this->params()->fromQuery('fra', null);
        $toString = $this->params()->fromQuery('til', null);

        $fromDate = $fromString ? new \DateTime($fromString) : null ;
        $toDate = $toString ? new \DateTime($toString) : null ;

        $frequencyData = (object) [
            'voting' => $this->voteService->getFrequencyByAssemblyAndCongressman($assemblyId, $congressmanId, $fromDate, $toDate),
            'voting_total' => $this->voteService->countByAssembly($assemblyId), //TODO remove
            'sessions' => $this->sessionService->fetchByAssemblyAndCongressman($assemblyId, $congressmanId),
            'issues' => $this->issueService->fetchByAssemblyAndCongressman($assemblyId, $congressmanId),
            'speech_time' => $this->speechService->countTotalTimeByAssemblyAndCongressman($assemblyId, $congressmanId),
            'categories' => $this->issueCategoryService->fetchFrequencyByAssemblyAndCongressman($assemblyId, $congressmanId),
            'vote_categories' => $this->voteItemService->fetchVoteByAssemblyAndCongressmanAndCategory($assemblyId, $congressmanId)
        ];

        return (new ItemModel($frequencyData))
            ->setStatus(200)
            ->setOption('Access-Control-Allow-Origin', '*');
    }

    public function assemblySessionsAction()
    {
        $assemblyId = $this->params('id');
        $congressmanId = $this->params('congressman_id');

        $sessions = $this->sessionService->fetchByAssemblyAndCongressman($assemblyId, $congressmanId);

        return (new CollectionModel($sessions))
            ->setStatus(200)
            ->setOption('Access-Control-Allow-Origin', '*');
    }

    public function assemblyIssuesAction()
    {
        $assemblyId = $this->params('id');
        $congressmanId = $this->params('congressman_id');

        $issues = $this->issueService->fetchByAssemblyAndCongressman($assemblyId, $congressmanId);

        return (new CollectionModel($issues))
            ->setStatus(200)
            ->setOption('Access-Control-Allow-Origin', '*');
    }

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
            ->setAllow(['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE'])
            ->setOption('Access-Control-Allow-Origin', '*')
            ->setOption('Access-Control-Allow-Headers', 'Range');
    }
    
    /**
     * @param Congressman $congressman
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
    }

    /**
     * @param Party $party
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
    }

    /**
     * @param Session $session
     */
    public function setSessionService(Session $session)
    {
        $this->sessionService = $session;
    }

    /**
     * @param Vote $vote
     */
    public function setVoteService(Vote $vote)
    {
        $this->voteService = $vote;
    }

    /**
     * @param Issue $issue
     */
    public function setIssueService(Issue $issue)
    {
        $this->issueService = $issue;
    }

    /**
     * @param Speech $speech
     */
    public function setSpeechService(Speech $speech)
    {
        $this->speechService = $speech;
    }

    /**
     * @param IssueCategory $issueCategory
     */
    public function setIssueCategoryService(IssueCategory $issueCategory)
    {
        $this->issueCategoryService = $issueCategory;
    }

    /**
     * @param VoteItem $voteItem
     */
    public function setVoteItemService(VoteItem $voteItem)
    {
        $this->voteItemService = $voteItem;
    }
}
