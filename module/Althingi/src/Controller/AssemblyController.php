<?php

namespace Althingi\Controller;

use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Injector\StoreCategoryAwareInterface;
use Althingi\Injector\StoreIssueAwareInterface;
use Althingi\Injector\StorePartyAwareInterface;
use Althingi\Injector\StoreSpeechAwareInterface;
use Althingi\Injector\StoreVoteAwareInterface;
use Althingi\Service\Congressman;
use Althingi\Store\Category;
use Althingi\Store\Issue;
use Althingi\Store\Party;
use Althingi\Store\Speech;
use Althingi\Store\Vote;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use Althingi\Utils\DateAndCountSequence;
use Althingi\Injector\ServiceAssemblyAwareInterface;
use Althingi\Injector\ServiceCabinetAwareInterface;
use Althingi\Injector\ServiceCategoryAwareInterface;
use Althingi\Injector\ServiceElectionAwareInterface;
use Althingi\Injector\ServiceIssueAwareInterface;
use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Injector\ServiceSpeechAwareInterface;
use Althingi\Injector\ServiceVoteAwareInterface;
use Althingi\Injector\StoreAssemblyAwareInterface;
use Althingi\Form;
use Althingi\Model;
use Althingi\Service;
use Althingi\Store;

class AssemblyController extends AbstractRestfulController implements
    ServiceAssemblyAwareInterface,
    ServiceIssueAwareInterface,
    ServicePartyAwareInterface,
    ServiceCongressmanAwareInterface,
    ServiceVoteAwareInterface,
    ServiceSpeechAwareInterface,
    ServiceCabinetAwareInterface,
    ServiceCategoryAwareInterface,
    ServiceElectionAwareInterface,
    StoreAssemblyAwareInterface,
    StoreIssueAwareInterface,
    StoreVoteAwareInterface,
    StoreSpeechAwareInterface,
    StorePartyAwareInterface,
    StoreCategoryAwareInterface
{
    use Range;

    /** @var $assemblyService \Althingi\Service\Assembly */
    private $assemblyService;

    /** @var $issueService \Althingi\Service\Issue */
    private $issueService;

    /** @var $voteService \Althingi\Service\Vote */
    private $voteService;

    /** @var $speechService \Althingi\Service\Speech */
    private $speechService;

    /** @var $partyService \Althingi\Service\Party */
    private $partyService;

    /** @var $congressmanService \Althingi\Service\Congressman */
    private $congressmanService;

    /** @var $cabinetService \Althingi\Service\Cabinet */
    private $cabinetService;

    /** @var $categoryService \Althingi\Service\Category */
    private $categoryService;

    /** @var $electionService \Althingi\Service\Election */
    private $electionService;

    /** @var $assemblyStore \Althingi\Store\Assembly */
    private $assemblyStore;

    /** @var $issueStore \Althingi\Store\Issue */
    private $issueStore;

    /** @var $voteStore \Althingi\Store\Vote */
    private $voteStore;

    /** @var $speechStore \Althingi\Store\Speech */
    private $speechStore;

    /** @var $partyStore \Althingi\Store\Party */
    private $partyStore;

    /** @var $categoryStore \Althingi\Store\Category */
    private $categoryStore;

    /**
     * Get one Assembly.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface|array
     * @output \Althingi\Model\AssemblyProperties
     */
    public function get($id)
    {
        if (($assembly = $this->assemblyService->get($id)) != null) {
            $assemblyProperties = (new Model\AssemblyProperties())
                ->setAssembly($assembly);
            $cabinets = $this->cabinetService->fetchByAssembly($assembly->getAssemblyId());
            $assemblyProperties->setCabinet(count($cabinets) > 0 ? $cabinets[0] : null);

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
     * @output \Althingi\Model\AssemblyProperties[]
     * @query order asc|desc
     */
    public function getList()
    {
        $order = $this->params()->fromQuery('order', 'desc');

        $count = $this->assemblyService->count();
        $range = $this->getRange($this->getRequest(), $count);
        $assemblies = $this->assemblyService->fetchAll(
            $range->getFrom(),
            $range->getSize(),
            $order
        );

        $assemblyCollection = array_map(function (Model\Assembly $assembly) {
            $assemblyProperties = (new Model\AssemblyProperties())
                ->setAssembly($assembly);
            $cabinets = $this->cabinetService->fetchByAssembly($assembly->getAssemblyId());

            foreach ($cabinets as $cabinet) {
                $assemblyProperties->setMajority($this->partyService->fetchByCabinet(
                    $cabinet->getCabinetId()
                ));
                $assemblyProperties->setMinority($this->partyService->fetchByAssembly(
                    $assembly->getAssemblyId(),
                    array_map(function (Model\Party $party) {
                        return $party->getPartyId();
                    }, $assemblyProperties->getMajority())
                ));
            }

            return $assemblyProperties;
        }, $assemblies);

        return (new CollectionModel($assemblyCollection))
            ->setStatus(206)
            ->setRange($range->getFrom(), $range->getFrom() + count($assemblyCollection), $count);
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
     * @output \Althingi\Model\AssemblyStatusProperties
     * @query category
     */
    public function statisticsAction()
    {
        $assembly = $this->assemblyService->get($this->params('id'));
//        $response = $this->fetchStatisticsFromService($assembly);
        $response = $this->fetchStatisticsFromStore($assembly);

        return (new ItemModel($response))
            ->setStatus(200)
            ->setOption('X-Source', 'Store');
    }

    /**
     * Create new Resource Assembly.
     *
     * @param  int $id
     * @param  array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Assembly
     */
    public function put($id, $data)
    {
        $form = new Form\Assembly();
        $form->bindValues(array_merge($data, ['assembly_id' => $id]));

        if ($form->isValid()) {
            $object = $form->getObject();
            $affectedRows = $this->assemblyService->save($object);
            return (new EmptyModel())
                ->setStatus($affectedRows === 1 ? 201 : 205);
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
     * @input \Althingi\Form\Assembly
     */
    public function patch($id, $data)
    {
        if (($assembly = $this->assemblyService->get($id)) != null) {
            $form = new Form\Assembly();
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
     * @param \Althingi\Service\Assembly $assembly
     * @return $this
     */
    public function setAssemblyService(Service\Assembly $assembly)
    {
        $this->assemblyService = $assembly;
        return $this;
    }

    /**
     * @param \Althingi\Service\Issue $issue
     * @return $this
     */
    public function setIssueService(Service\Issue $issue)
    {
        $this->issueService = $issue;
        return $this;
    }

    /**
     * @param \Althingi\Service\Party $party
     * @return $this
     */
    public function setPartyService(Service\Party $party)
    {
        $this->partyService = $party;
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
     * @param \Althingi\Service\Vote $vote
     * @return $this
     */
    public function setVoteService(Service\Vote $vote)
    {
        $this->voteService = $vote;
        return $this;
    }

    /**
     * @param \Althingi\Service\Cabinet $cabinet
     * @return $this;
     */
    public function setCabinetService(Service\Cabinet $cabinet)
    {
        $this->cabinetService = $cabinet;
        return $this;
    }

    /**
     * @param \Althingi\Service\Category $category
     * @return $this
     */
    public function setCategoryService(Service\Category $category)
    {
        $this->categoryService = $category;
        return $this;
    }

    /**
     * @param \Althingi\Service\Election $election
     * @return $this
     */
    public function setElectionService(Service\Election $election)
    {
        $this->electionService = $election;
        return $this;
    }

    /**
     * @param \Althingi\Store\Assembly $assembly
     * @return $this
     */
    public function setAssemblyStore(Store\Assembly $assembly)
    {
        $this->assemblyStore = $assembly;
        return $this;
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
     * @param \Althingi\Store\Issue $issue
     * @return $this
     */
    public function setIssueStore(Issue $issue)
    {
        $this->issueStore = $issue;
        return $this;
    }

    /**
     * @param \Althingi\Store\Vote $vote
     * @return $this;
     */
    public function setVoteStore(Vote $vote)
    {
        $this->voteStore = $vote;
        return $this;
    }

    /**
     * @param \Althingi\Store\Speech $speech
     * @return $this;
     */
    public function setSpeechStore(Speech $speech)
    {
        $this->speechStore = $speech;
        return $this;
    }

    /**
     * @param \Althingi\Store\Party $party
     * @return $this;
     */
    public function setPartyStore(Party $party)
    {
        $this->partyStore = $party;
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
     * Gets Statistics from Service ie. DB.
     *
     * @param Model\Assembly $assembly
     * @return Model\AssemblyStatusProperties
     * @deprecated
     */
    private function fetchStatisticsFromService(Model\Assembly $assembly)
    {
        return (new Model\AssemblyStatusProperties())
            ->setVotes(DateAndCountSequence::buildDateRange(
                $assembly->getFrom(),
                $assembly->getTo(),
                $this->voteService->fetchFrequencyByAssembly($assembly->getAssemblyId())
            ))
            ->setSpeeches(DateAndCountSequence::buildDateRange(
                $assembly->getFrom(),
                $assembly->getTo(),
                $this->speechService->fetchFrequencyByAssembly($assembly->getAssemblyId(), ['A', 'B'])
            ))
            ->setAverageAge($this->congressmanService->getAverageAgeByAssembly(
                $assembly->getAssemblyId(),
                $assembly->getFrom()
            ))
            ->setPartyTimes($this->partyService->fetchTimeByAssembly($assembly->getAssemblyId(), ['A', 'B']))
            ->setElection($this->electionService->getByAssembly($assembly->getAssemblyId()))
            ->setElectionResults($this->partyService->fetchElectedByAssembly($assembly->getAssemblyId()))
        ;
    }

    /**
     * Gets Statistics from Service ie. MongoDB.
     *
     * @param Model\Assembly $assembly
     * @return Model\AssemblyStatusProperties
     * @todo the average-age is still being calculated from the DB.
     * @todo the Election results are not being fetched at all.
     */
    private function fetchStatisticsFromStore(Model\Assembly $assembly)
    {
        return (new Model\AssemblyStatusProperties())
            ->setVotes($this->voteStore->fetchFrequencyByAssembly($assembly->getAssemblyId()))
            ->setSpeeches($this->speechStore->fetchFrequencyByAssembly($assembly->getAssemblyId()))
            ->setAverageAge($this->congressmanService->getAverageAgeByAssembly(
                $assembly->getAssemblyId(),
                $assembly->getFrom()
            ))
            ->setPartyTimes($this->partyStore->fetchTimeByAssembly($assembly->getAssemblyId()))

            ->setElection(null)
            ->setElectionResults([])
        ;
    }
}
