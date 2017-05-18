<?php

namespace Althingi\Controller;

use Althingi\Form\Assembly as AssemblyForm;
use Althingi\Lib\ServiceAssemblyAwareInterface;
use Althingi\Lib\ServiceCabinetAwareInterface;
use Althingi\Lib\ServiceCategoryAwareInterface;
use Althingi\Lib\ServiceElectionAwareInterface;
use Althingi\Lib\ServiceIssueAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Lib\ServiceSpeechAwareInterface;
use Althingi\Lib\ServiceVoteAwareInterface;
use Althingi\Model\AssemblyProperties;
use Althingi\Service\Assembly;
use Althingi\Service\Cabinet;
use Althingi\Service\Category;
use Althingi\Service\Election;
use Althingi\Service\Issue;
use Althingi\Service\Party;
use Althingi\Service\Speech;
use Althingi\Service\Vote;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;

class AssemblyController extends AbstractRestfulController implements
    ServiceAssemblyAwareInterface,
    ServiceIssueAwareInterface,
    ServicePartyAwareInterface,
    ServiceVoteAwareInterface,
    ServiceSpeechAwareInterface,
    ServiceCabinetAwareInterface,
    ServiceCategoryAwareInterface,
    ServiceElectionAwareInterface
{
    use Range;

    /** @var $assemblyService \Althingi\Service\Assembly */
    private $assemblyService;

    /** @var $issueService \Althingi\Service\Issue */
    private $issueService;

    /** @var $issueService \Althingi\Service\Vote */
    private $voteService;

    /** @var $issueService \Althingi\Service\Speech */
    private $speechService;

    /** @var $issueService \Althingi\Service\Party */
    private $partyService;

    /** @var $issueService \Althingi\Service\Cabinet */
    private $cabinetService;

    /** @var $issueService \Althingi\Service\Category */
    private $categoryService;

    /** @var $issueService \Althingi\Service\Election */
    private $electionService;

    /**
     * Get one Assembly.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface|array
     */
    public function get($id)
    {
        if (($assembly = $this->assemblyService->get($id)) != null) {
            $assemblyProperties = (new AssemblyProperties())
                ->setAssembly($assembly);
            $cabinets = $this->cabinetService->fetchByAssembly($assembly->getAssemblyId());

            foreach ($cabinets as $cabinet) {
                $assemblyProperties->setMajority(
                    $this->partyService->fetchByCabinet($cabinet->getCabinetId())
                );
                $assemblyProperties->setMinority(
                    $this->partyService->fetchByAssembly(
                        $assembly->getAssemblyId(),
                        $assemblyProperties->getMajorityPartyIds()
                    )
                );
            }

            return (new ItemModel($assemblyProperties))
                ->setStatus(200);
        }

        return $this->notFoundAction();
    }

    /**
     * Return list of Assemblies.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function getList()
    {
        $order = $this->params()->fromQuery('order', 'desc');

        $count = $this->assemblyService->count();
        $range = $this->getRange($this->getRequest(), $count);
        $assemblies = $this->assemblyService->fetchAll($range['from'], ($range['to'] - $range['from']), $order);

        $assemblyCollection = array_map(function (\Althingi\Model\Assembly $assembly) {
            $assemblyProperties = (new AssemblyProperties())
                ->setAssembly($assembly);
            $cabinets = $this->cabinetService->fetchByAssembly($assembly->getAssemblyId());

            foreach ($cabinets as $cabinet) {
                $assemblyProperties->setMajority($this->partyService->fetchByCabinet(
                    $cabinet->getCabinetId()
                ));
                $assemblyProperties->setMinority($this->partyService->fetchByAssembly(
                    $assembly->getAssemblyId(),
                    array_map(function (\Althingi\Model\Party $party) {
                        return $party->getPartyId();
                    }, $assemblyProperties->getMajority())
                ));
            }

            return $assemblyProperties;
        }, $assemblies);

        return (new CollectionModel($assemblyCollection))
            ->setStatus(206)
            ->setRange(0, $count, $count);
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
            ->setAllow(['GET', 'OPTIONS']);
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
            ->setAllow(['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE']);
    }

    /**
     * Get statistics about assembly.
     *
     * @return \Rend\View\Model\ModelInterface
     */
    public function statisticsAction()
    {
        $assemblyId = $this->params('id');

        $response = (object)[
            'bills' => $this->issueService->fetchNonGovernmentBillStatisticsByAssembly($assemblyId),
            'government_bills' => $this->issueService->fetchGovernmentBillStatisticsByAssembly($assemblyId),
            'types' => $this->issueService->fetchStateByAssembly($assemblyId),
            'votes' => $this->voteService->fetchFrequencyByAssembly($assemblyId),
            'speeches' => $this->speechService->fetchFrequencyByAssembly($assemblyId),
            'party_times' => $this->partyService->fetchTimeByAssembly($assemblyId),
            'categories' => $this->categoryService->fetchByAssembly($assemblyId),
            'election' => $this->electionService->getByAssembly($assemblyId),
            'election_results' => $this->partyService->fetchElectedByAssembly($assemblyId)
        ];

        return (new ItemModel($response))
            ->setStatus(200);
    }

    /**
     * Create new Resource Assembly.
     *
     * @param  int $id
     * @param  array $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function put($id, $data)
    {
        $form = new AssemblyForm();
        $form->bindValues(array_merge($data, ['assembly_id' => $id]));

        if ($form->isValid()) {
            $object = $form->getObject();
            $this->assemblyService->create($object);
            return (new EmptyModel())
                ->setStatus(201);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * Update one Assembly
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function patch($id, $data)
    {
        if (($assembly = $this->assemblyService->get($id)) != null) {
            $form = new AssemblyForm();
            $form->bind($assembly);
            $form->setData($data);

            if ($form->isValid()) {
                $this->assemblyService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * @param Assembly $assembly
     */
    public function setAssemblyService(Assembly $assembly)
    {
        $this->assemblyService = $assembly;
    }

    /**
     * @param Issue $issue
     */
    public function setIssueService(Issue $issue)
    {
        $this->issueService = $issue;
    }

    /**
     * @param Party $party
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
    }

    /**
     * @param Speech $speech
     */
    public function setSpeechService(Speech $speech)
    {
        $this->speechService = $speech;
    }

    /**
     * @param Vote $vote
     */
    public function setVoteService(Vote $vote)
    {
        $this->voteService = $vote;
    }

    /**
     * @param Cabinet $cabinet
     */
    public function setCabinetService(Cabinet $cabinet)
    {
        $this->cabinetService = $cabinet;
    }

    /**
     * @param Category $category
     */
    public function setCategoryService(Category $category)
    {
        $this->categoryService = $category;
    }

    /**
     * @param Election $election
     */
    public function setElectionService(Election $election)
    {
        $this->electionService = $election;
    }
}
