<?php

namespace Althingi\Controller;

use Althingi\Form;
use Althingi\Injector\ServiceAssemblyAwareInterface;
use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Injector\ServiceConstituencyAwareInterface;
use Althingi\Injector\ServiceIssueAwareInterface;
use Althingi\Injector\ServiceIssueCategoryAwareInterface;
use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Injector\ServiceSessionAwareInterface;
use Althingi\Injector\ServiceSpeechAwareInterface;
use Althingi\Injector\ServiceVoteAwareInterface;
use Althingi\Injector\ServiceVoteItemAwareInterface;
use Althingi\Injector\StoreCongressmanAwareInterface;
use Althingi\Model\CongressmanAndParties;
use Althingi\Model\CongressmanAndParty;
use Althingi\Model\CongressmanPartiesProperties;
use Althingi\Model\CongressmanPartyProperties;
use Althingi\Model;
use Althingi\Service\Assembly;
use Althingi\Service\Congressman;
use Althingi\Service\Constituency;
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
    ServiceIssueCategoryAwareInterface,
    ServiceAssemblyAwareInterface,
    ServiceConstituencyAwareInterface,
    StoreCongressmanAwareInterface
{
    use Range;

    /** @var \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var \Althingi\Service\Party */
    private $partyService;

    /** @var \Althingi\Service\Assembly */
    private $assemblyService;

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

    /** @var \Althingi\Service\Constituency */
    private $constituencyService;

    /** @var \Althingi\Store\Congressman */
    private $congressmanStore;

    /**
     * Get one congressman.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CongressmanAndParties
     */
    public function get($id)
    {
        if ($congressman = $this->congressmanService->get($id)) {
            $congressmanWithParties = (new CongressmanAndParties())
                ->setCongressman($congressman)
                ->setParties($this->partyService->fetchByCongressman($id));

            return (new ItemModel($congressmanWithParties))
                ->setStatus(200);
        }

        return $this->notFoundAction();
    }

    /**
     * Return list of congressmen.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CongressmanAndParties[]
     */
    public function getList()
    {
        $count = $this->congressmanService->count();
        $range = $this->getRange($this->getRequest(), $count);
        $congressmen = $this->congressmanService->fetchAll($range->getFrom(), $range->getTo());

        return (new CollectionModel($congressmen))
            ->setStatus(206)
            ->setRange($range->getFrom(), $range->getFrom() + count($congressmen), $count);
    }

    /**
     * Create on congressman entry.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Congressman
     */
    public function put($id, $data)
    {
        $form = new Form\Congressman();
        $form->setData(array_merge($data, ['congressman_id' => $id]));

        if ($form->isValid()) {
            $affectedRows = $this->congressmanService->save($form->getObject());
            return (new EmptyModel())
                ->setStatus($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * Update congressman.
     *
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Congressman
     */
    public function patch($id, $data)
    {
        if (($congressman = $this->congressmanService->get($id)) != null) {
            $form = (new Form\Congressman())
                ->bind($congressman)
                ->setData($data);

            if ($form->isValid()) {
                $this->congressmanService->update($form->getObject());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
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
                ->setStatus(205)
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return $this->notFoundAction();
    }

    /**
     * Get all members of assembly.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CongressmanPartyProperties[]
     * @query tegund thingmadur|varamadur
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

        $assembly = $this->assemblyService->get($assemblyId);
        $congressmen = array_map(function (CongressmanAndParty $congressman) use ($assembly) {
            return (new CongressmanPartiesProperties())
                ->setCongressman($congressman)
                ->setConstituency($this->constituencyService->getByAssemblyAndCongressman(
                    $congressman->getCongressmanId(),
                    $assembly->getAssemblyId()
                ))->setParties($this->partyService->fetchByCongressmanAndAssembly(
                    $congressman->getCongressmanId(),
                    $assembly->getAssemblyId()
                ))->setAssembly($assembly);
        }, $this->congressmanService->fetchByAssembly($assemblyId, $typeParam));
        $congressmenCount = count($congressmen);

        return (new CollectionModel($congressmen))
            ->setStatus(206)
            ->setRange(0, $congressmenCount, $congressmenCount);
    }

    /**
     * Gets a list of congressmen and accumulated speech times.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CongressmanPartyProperties[]
     * @query rod asc|desc
     * @query fjoldi [number]
     * @query category
     */
    public function assemblyTimesAction()
    {
        $assemblyId = $this->params('id');
        $order = $this->params()->fromQuery('rod', 'desc');
        $size = $this->params()->fromQuery('fjoldi', 5);

        $collection = $this->fetchAssemblyTimeFromStore($assemblyId, $size, $order === 'desc' ? -1 : 1);
//        $collection = $this->fetchAssemblyTimeFromService($assemblyId, $size, $order);

        return (new CollectionModel($collection))
            ->setStatus(206)
            ->setRange(0, count($collection), count($collection))
            ->setOption('X-Source', 'Store');
    }

    /**
     * Gets a list of congressmen and accumulated count of questions they have submitted.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CongressmanPartyProperties[]
     * @query rod asc|desc
     * @query fjoldi [number]
     */
    public function assemblyQuestionsAction()
    {
        $assemblyId = $this->params('id');
        $order = $this->params()->fromQuery('rod', 'desc');
        $size = $this->params()->fromQuery('fjoldi', 5);

        $collection = $this->fetchAssemblyQuestionsFromStore($assemblyId, $size, $order === 'desc' ? -1 : 1);
//        $collection = $this->fetchAssemblyQuestionsFromService($assemblyId, $size, $order);

        return (new CollectionModel($collection))
            ->setStatus(206)
            ->setRange(0, count($collection), count($collection))
            ->setOption('X-Source', 'Store');
    }

    /**
     * Gets a list of congressmen and accumulated count of propositions they have submitted.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CongressmanPartyProperties[]
     * @query rod asc|desc
     * @query fjoldi [number]
     */
    public function assemblyPropositionsAction()
    {
        $assemblyId = $this->params('id');
        $order = $this->params()->fromQuery('rod', 'desc');
        $size = $this->params()->fromQuery('fjoldi', 5);

        $congressman = $this->fetchPropositionsFromStore($assemblyId, $size, $order);
//        $congressman = $this->fetchPropositionsFromService($assemblyId, $size, $order);

        return (new CollectionModel($congressman))
            ->setStatus(206)
            ->setRange(0, count($congressman), count($congressman))
            ->setOption('X-Source', 'Store');
    }

    /**
     * Gets a list of congressmen and accumulated count of bills they have submitted.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CongressmanPartyProperties[]
     * @query rod asc|desc
     * @query fjoldi [number]
     */
    public function assemblyBillsAction()
    {
        $assemblyId = $this->params('id');
        $order = $this->params()->fromQuery('rod', 'desc');
        $size = $this->params()->fromQuery('fjoldi', 5);

        $congressman = $this->fetchBillsFromStore($assemblyId, $size, $order);
//        $congressman = $this->fetchBillsFromService($assemblyId, $size, $order);

        return (new CollectionModel($congressman))
            ->setStatus(206)
            ->setRange(0, count($congressman), count($congressman))
            ->setOption('X-Source', 'Store');
    }

    public function assemblySessionsAction()
    {
        $assemblyId = $this->params('id');
        $congressmanId = $this->params('congressman_id');

        $sessions = $this->sessionService->fetchByAssemblyAndCongressman($assemblyId, $congressmanId);
        $sessionsCount = count($sessions);

        return (new CollectionModel($sessions))
            ->setStatus(206)
            ->setRange(0, $sessionsCount, $sessionsCount);
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Issue[]
     */
    public function assemblyIssuesAction()
    {
        $assemblyId = $this->params('id');
        $congressmanId = $this->params('congressman_id');

        $issues = $this->issueService->fetchByAssemblyAndCongressman($assemblyId, $congressmanId);
        $issuesCount = count($issues);

        return (new CollectionModel($issues))
            ->setStatus(206)
            ->setRange(0, $issuesCount, $issuesCount);
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Issue[]
     */
    public function assemblyIssuesSummaryAction()
    {
        $assemblyId = $this->params('id');
        $congressmanId = $this->params('congressman_id');

        $issues = $this->issueService->fetchByAssemblyAndCongressmanSummary($assemblyId, $congressmanId);
        $issuesCount = count($issues);

        return (new CollectionModel($issues))
            ->setStatus(206)
            ->setRange(0, $issuesCount, $issuesCount);
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\VoteTypeAndCount[]
     * @query fra [date]
     * @query til [date]
     */
    public function assemblyVotingAction()
    {
        $fromString = $this->params()->fromQuery('fra', null);
        $toString = $this->params()->fromQuery('til', null);

        $fromDate = $fromString ? new \DateTime($fromString) : null ;
        $toDate = $toString ? new \DateTime($toString) : null ;
        $assemblyId = (int) $this->params('id');
        $congressmanId = (int) $this->params('congressman_id');

        $voting = $this->voteService->getFrequencyByAssemblyAndCongressman(
            $assemblyId,
            $congressmanId,
            $fromDate,
            $toDate
        );
        $votingCount = count($voting);

        return (new CollectionModel($voting))
            ->setStatus(206)
            ->setRange(0, $votingCount, $votingCount);
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\IssueCategoryAndTime[]
     * @query category
     */
    public function assemblyCategoriesAction()
    {
        $assemblyId = $this->params('id');
        $congressmanId = $this->params('congressman_id');

        $categories = $this->issueCategoryService->fetchFrequencyByAssemblyAndCongressman(
            $assemblyId,
            $congressmanId,
            ['A', 'B']
        );
        $categoriesCount = count($categories);

        return (new CollectionModel($categories))
            ->setStatus(206)
            ->setRange(0, $categoriesCount, $categoriesCount);
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\VoteItem[]
     */
    public function assemblyVoteCategoriesAction()
    {
        $assemblyId = $this->params('id');
        $congressmanId = $this->params('congressman_id');

        $voteCategories = $this->voteItemService->fetchVoteByAssemblyAndCongressmanAndCategory(
            $assemblyId,
            $congressmanId
        );
        $voteCategoriesCount = count($voteCategories);

        return (new CollectionModel($voteCategories))
            ->setStatus(206)
            ->setRange(0, $voteCategoriesCount, $voteCategoriesCount);
    }

    public function optionsList()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS'])
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
            ->setOption('Access-Control-Allow-Headers', 'Range');
    }

    /**
     * @param Congressman $congressman
     * @return $this
     */
    public function setCongressmanService(Congressman $congressman)
    {
        $this->congressmanService = $congressman;
        return $this;
    }

    /**
     * @param Party $party
     * @return $this
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
        return $this;
    }

    /**
     * @param Session $session
     * @return $this
     */
    public function setSessionService(Session $session)
    {
        $this->sessionService = $session;
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
     * @param Issue $issue
     * @return $this
     */
    public function setIssueService(Issue $issue)
    {
        $this->issueService = $issue;
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
     * @param IssueCategory $issueCategory
     * @return $this
     */
    public function setIssueCategoryService(IssueCategory $issueCategory)
    {
        $this->issueCategoryService = $issueCategory;
        return $this;
    }

    /**
     * @param VoteItem $voteItem
     * @return $this
     */
    public function setVoteItemService(VoteItem $voteItem)
    {
        $this->voteItemService = $voteItem;
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
     * @param Constituency $constituency
     * @return $this
     */
    public function setConstituencyService(Constituency $constituency)
    {
        $this->constituencyService = $constituency;
        return $this;
    }

    /**
     * @param \Althingi\Store\Congressman $congressman
     * @return $this;
     */
    public function setCongressmanStore(\Althingi\Store\Congressman $congressman)
    {
        $this->congressmanStore = $congressman;
        return $this;
    }

    /**
     * Gets top X speakers for an assembly from the Store
     *
     * @param int $assemblyId
     * @param int $size
     * @param int $order
     * @return array
     */
    private function fetchAssemblyTimeFromStore(int $assemblyId, int $size, int $order): array
    {
        return $this->congressmanStore->fetchTimeByAssembly($assemblyId, $size, $order);
    }

    /**
     * Gets top X speakers for an assembly from the Service
     *
     * @param int $assemblyId
     * @param int $size
     * @param null|string $order
     * @return array
     * @deprecated
     * @see CongressmanController::fetchAssemblyTimeFromStore
     */
    private function fetchAssemblyTimeFromService(int $assemblyId, int $size, ?string $order): array
    {
        $assembly = $this->assemblyService->get($assemblyId);
        $congressmen = $this->congressmanService->fetchTimeByAssembly($assemblyId, $size, $order, ['A', 'B']);

        return array_map(function (\Althingi\Model\Congressman $congressman) use ($assembly) {
            return (new CongressmanPartyProperties())
                ->setCongressman($congressman)
                ->setAssembly($assembly)
                ->setParty(
                    $this->partyService->getByCongressman($congressman->getCongressmanId(), $assembly->getFrom())
                );
        }, $congressmen);
    }

    /**
     * @param int $assemblyId
     * @param int $size
     * @param int $order
     * @return array
     */
    private function fetchAssemblyQuestionsFromStore(int $assemblyId, int $size, int $order): array
    {
        return $this->congressmanStore->fetchQuestionByAssembly($assemblyId, $size, $order);
    }

    /**
     * @param int $assemblyId
     * @param int $size
     * @param int $order
     * @return array
     * @deprecated
     */
    private function fetchAssemblyQuestionsFromService(int $assemblyId, int $size, string $order): array
    {
        $assembly = $this->assemblyService->get($assemblyId);
        $congressmen = $this->congressmanService->fetchIssueTypeCountByAssembly(
            $assemblyId,
            $size,
            ['q', 'm'],
            $order
        );

        return array_map(function (\Althingi\Model\Congressman $congressman) use ($assembly) {
            return (new CongressmanPartyProperties())
                ->setCongressman($congressman)
                ->setAssembly($assembly)
                ->setParty(
                    $this->partyService->getByCongressman($congressman->getCongressmanId(), $assembly->getFrom())
                );
        }, $congressmen);
    }

    /**
     * @param int $assemblyId
     * @param int $size
     * @param string $order
     * @return array
     * @deprecated
     */
    public function fetchPropositionsFromService(int $assemblyId, int $size, string $order): array
    {
        $assembly = $this->assemblyService->get($assemblyId);
        $congressmen = $this->congressmanService->fetchIssueTypeCountByAssembly(
            $assembly->getAssemblyId(),
            $size,
            ['a'],
            $order
        );

        return array_map(function (Model\Congressman $congressman) use ($assembly) {
            return (new CongressmanPartyProperties())
                ->setCongressman($congressman)
                ->setAssembly($assembly)
                ->setParty(
                    $this->partyService->getByCongressman($congressman->getCongressmanId(), $assembly->getFrom())
                );
        }, $congressmen);
    }

    /**
     * @param int $assemblyId
     * @param int $size
     * @param string $order
     * @return array
     */
    public function fetchPropositionsFromStore(int $assemblyId, int $size, string $order)
    {
        return $this->congressmanStore->fetchPropositionsByAssembly($assemblyId, $size);
    }

    /**
     * @param int $assemblyId
     * @param int $size
     * @param string $order
     * @return array
     * @deprecated
     */
    private function fetchBillsFromService(int $assemblyId, int $size, string $order): array
    {
        $assembly = $this->assemblyService->get($assemblyId);
        $congressmen = $this->congressmanService->fetchIssueTypeCountByAssembly($assemblyId, $size, ['l'], $order);
        return array_map(function (Model\Congressman $congressman) use ($assembly) {
            return (new CongressmanPartyProperties())
                ->setCongressman($congressman)
                ->setAssembly($assembly)
                ->setParty(
                    $this->partyService->getByCongressman($congressman->getCongressmanId(), $assembly->getFrom())
                );
        }, $congressmen);
    }

    private function fetchBillsFromStore(int $assemblyId, int $size, string $order): array
    {
        return $this->congressmanStore->fetchBillsByAssembly($assemblyId, $size, $order === 'desc' ? -1 : 1);
    }
}
