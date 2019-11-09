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
use Althingi\Injector\StoreIssueAwareInterface;
use Althingi\Injector\StoreSessionAwareInterface;
use Althingi\Injector\StoreVoteAwareInterface;
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
    ServiceVoteItemAwareInterface,
    ServiceAssemblyAwareInterface,
    StoreCongressmanAwareInterface,
    StoreSessionAwareInterface,
    StoreVoteAwareInterface,
    StoreIssueAwareInterface
{
    use Range;

    /** @var \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var \Althingi\Service\Party */
    private $partyService;

    /** @var \Althingi\Service\Assembly */
    private $assemblyService;

    /** @var \Althingi\Service\VoteItem */
    private $voteItemService;

    /** @var \Althingi\Store\Congressman */
    private $congressmanStore;

    /** @var \Althingi\Store\Session */
    private $sessionStore;

    /** @var \Althingi\Store\Vote */
    private $voteStore;

    /** @var \Althingi\Store\Issue */
    private $issueStore;

    /**
     * Get one congressman.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\CongressmanAndParties
     * @todo this should use the store.
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

        return (new ErrorModel('Resource Not Found'))
            ->setStatus(404);
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
     * @todo assemblyService should be store
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
        $congressmen = $this->congressmanStore->fetchByAssembly($assembly->getAssemblyId(), $typeQuery);

        $congressmenCount = count($congressmen);
        return (new CollectionModel($congressmen))
            ->setStatus(206)
            ->setRange(0, $congressmenCount, $congressmenCount);
    }

    /**
     * Gets a single Congressman in an assembly including all parties
     * and constituency.
     *
     * @output \Althingi\Model\CongressmanPartyProperties
     * @return ItemModel
     */
    public function assemblyCongressmanAction()
    {
        $assemblyId = $this->params('id');
        $congressmanId = $this->params('congressman_id');

        if (($congressman = $this->congressmanStore->getByAssembly($assemblyId, $congressmanId)) !== null) {
            return (new ItemModel($congressman))
                ->setStatus(200);
        }

        return  (new ErrorModel('Resource Not Found'))
            ->setStatus(404);
    }

    /**
     * Get name/count of types of documents that are not primary
     * documents per congressman.
     *
     * @output \Althingi\Althingi\Model\ValueAndCount[]
     * @return CollectionModel
     */
    public function assemblyCongressmanOtherDocsAction()
    {
        $assemblyId = $this->params('id');
        $congressmanId = $this->params('congressman_id');
        $valueCounts = $this->congressmanStore->fetchOtherDocumentsByAssembly($assemblyId, $congressmanId);

        return (new CollectionModel($valueCounts))
            ->setStatus(206)
            ->setRange(0, count($valueCounts), count($valueCounts));
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

        $collection = $this->congressmanStore->fetchTimeByAssembly($assemblyId, $size, $order === 'desc' ? -1 : 1);

        return (new CollectionModel($collection))
            ->setStatus(206)
            ->setRange(0, count($collection), count($collection));
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

        $collection = $this->congressmanStore->fetchQuestionByAssembly($assemblyId, $size, $order === 'desc' ? -1 : 1);

        return (new CollectionModel($collection))
            ->setStatus(206)
            ->setRange(0, count($collection), count($collection));
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

        $congressman = $this->congressmanStore->fetchPropositionsByAssembly($assemblyId, $size);

        return (new CollectionModel($congressman))
            ->setStatus(206)
            ->setRange(0, count($congressman), count($congressman));
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

        $congressman = $this->congressmanStore->fetchBillsByAssembly($assemblyId, $size, $order === 'desc' ? -1 : 1);

        return (new CollectionModel($congressman))
            ->setStatus(206)
            ->setRange(0, count($congressman), count($congressman));
    }

    /**
     * Get sessions array for a congressman per assembly.
     *
     * @return CollectionModel
     * @output \Althingi\Model\Session[]
     */
    public function assemblySessionsAction()
    {
        $assemblyId = $this->params('id');
        $congressmanId = $this->params('congressman_id');

        $sessions = $this->sessionStore->fetchByAssemblyAndCongressman($assemblyId, $congressmanId);
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

        $issues = $this->issueStore->fetchByAssemblyAndCongressman($assemblyId, $congressmanId);
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

        $issues = $this->issueStore->fetchByAssemblyAndCongressmanSummary($assemblyId, $congressmanId);
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
     * @throws \Exception
     */
    public function assemblyVotingAction()
    {
        $fromString = $this->params()->fromQuery('fra', null);
        $toString = $this->params()->fromQuery('til', null);

        $fromDate = $fromString ? new \DateTime($fromString) : null ;
        $toDate = $toString ? new \DateTime($toString) : null ;
        $assemblyId = (int) $this->params('id');
        $congressmanId = (int) $this->params('congressman_id');

        $voting = $this->voteStore->getFrequencyByAssemblyAndCongressman($assemblyId, $congressmanId);
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

        $categories = $this->issueStore->fetchFrequencyByAssemblyAndCongressman($assemblyId, $congressmanId);
        $categoriesCount = count($categories);

        return (new CollectionModel($categories))
            ->setStatus(206)
            ->setRange(0, $categoriesCount, $categoriesCount);
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\VoteItem[]
     * @todo voteItemService should use store.
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

    /**
     * Get total speech time per congressman per assembly.
     *
     * @return ItemModel
     * @output \Althingi\Model\ValueAndCount
     */
    public function assemblySpeechTimeAction()
    {
        $assemblyId = $this->params('id');
        $congressmanId = $this->params('congressman_id');

        if (($time = $this->congressmanStore->getSpeechTimeByAssembly($assemblyId, $congressmanId)) !== null) {
            return (new ItemModel($time));
        }

        return (new ErrorModel('Resource Not Found'))
            ->setStatus(404);
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
     * @param \Althingi\Store\Congressman $congressman
     * @return $this
     */
    public function setCongressmanStore(\Althingi\Store\Congressman $congressman)
    {
        $this->congressmanStore = $congressman;
        return $this;
    }

    /**
     * @param \Althingi\Store\Session $session
     * @return $this
     */
    public function setSessionStore(\Althingi\Store\Session $session)
    {
        $this->sessionStore = $session;
        return $this;
    }

    /**
     * @param \Althingi\Store\Vote $vote
     * @return $this
     */
    public function setVoteStore(\Althingi\Store\Vote $vote)
    {
        $this->voteStore = $vote;
        return $this;
    }

    /**
     * @param \Althingi\Store\Issue $issue
     * @return $this
     */
    public function setIssueStore(\Althingi\Store\Issue $issue)
    {
        $this->issueStore = $issue;
        return $this;
    }
}
