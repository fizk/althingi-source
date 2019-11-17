<?php

namespace Althingi\Controller;

use Althingi\Injector\ServiceCongressmanAwareInterface;
use Althingi\Injector\StoreCategoryAwareInterface;
use Althingi\Injector\StoreCongressmanAwareInterface;
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
    StoreCategoryAwareInterface,
    StoreCongressmanAwareInterface
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

    /** @var $categoryStore \Althingi\Store\Congressman */
    private $congressmanStore;

    /**
     * Get one Assembly.
     *
     * @param int $id
     * @return \Rend\View\Model\ModelInterface|array
     * @output \Althingi\Model\AssemblyProperties
     * @404 not found
     * @200 Success
     * @todo Cabinet and parties are fetch from the DB
     */
    public function get($id)
    {
        if (($assembly = $this->assemblyStore->get($id)) != null) {
            $cabinets = $this->cabinetService->fetchByAssembly($assembly->getAssembly()->getAssemblyId());
            $assembly->setCabinet(count($cabinets) > 0 ? $cabinets[0] : null);

            foreach ($cabinets as $cabinet) {
                $assembly->setMajority(
                    $this->partyService->fetchByCabinet($cabinet->getCabinetId())
                );
                $assembly->setMinority(
                    $this->partyService->fetchByAssembly(
                        $assembly->getAssembly()->getAssemblyId(),
                        $assembly->getMajorityPartyIds()
                    )
                );
            }

            return (new ItemModel($assembly))
                ->setStatus(200);
        }

        return (new ErrorModel('Resource Not Found'))
            ->setStatus(404);
    }

    /**
     * Return list of Assemblies.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\AssemblyProperties[]
     * @206 Success
     * @todo Cabinet and parties are fetch from the DB
     */
    public function getList()
    {
        $assemblies = $this->assemblyStore->fetch();

        $assembliesProperties = array_map(function (Model\AssemblyProperties $assembly) {
            $cabinets = $this->cabinetService->fetchByAssembly($assembly->getAssembly()->getAssemblyId());
            $assembly->setCabinet(count($cabinets) > 0 ? $cabinets[0] : null);

            foreach ($cabinets as $cabinet) {
                $assembly->setMajority(
                    $this->partyService->fetchByCabinet($cabinet->getCabinetId())
                );
                $assembly->setMinority(
                    $this->partyService->fetchByAssembly(
                        $assembly->getAssembly()->getAssemblyId(),
                        $assembly->getMajorityPartyIds()
                    )
                );
            }
            return $assembly;
        }, $assemblies);

        return (new CollectionModel($assembliesProperties))
            ->setStatus(206)
            ->setRange(0, count($assembliesProperties), count($assembliesProperties));
    }

    /**
     * List options for Assembly collection.
     *
     * @return \Rend\View\Model\ModelInterface
     * @200 Success
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
     * @200 Success
     */
    public function options()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE']);
    }

    /**
     * Create new Resource Assembly.
     *
     * @param  int $id
     * @param  array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Assembly
     * @201 Created
     * @205 Updated
     * @400 Invalid input
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
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
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

        return (new ErrorModel('Resource Not Found'))
            ->setStatus(404);
    }

    /**
     * Get statistics about assembly.
     *
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\AssemblyStatusProperties
     * @query category
     * @200 Success
     * @404 Resource not found
     */
    public function statisticsAction()
    {
        if (($assembly = $this->assemblyService->get($this->params('id'))) !== null) {
            $response = (new Model\AssemblyStatusProperties())
                ->setVotes($this->voteStore->fetchFrequencyByAssembly($assembly->getAssemblyId()))
                ->setSpeeches($this->speechStore->fetchFrequencyByAssembly($assembly->getAssemblyId()))
                ->setAverageAge($this->congressmanStore->getAverageAgeByAssembly(
                    $assembly->getAssemblyId(),
                    $assembly->getFrom()
                ))
                ->setPartyTimes($this->partyStore->fetchTimeByAssembly($assembly->getAssemblyId()))
//            ->setElection($this->electionService->getByAssembly($assembly->getAssemblyId()))
                ->setElection(null)
                ->setElectionResults([])
            ;
            return (new ItemModel($response))
                ->setStatus(200);
        }

        return (new ErrorModel('Resourcenot found'))
            ->setStatus(404);
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
     * @param \Althingi\Store\Congressman $congressman
     * @return $this
     */
    public function setCongressmanStore(Store\Congressman $congressman)
    {
        $this->congressmanStore = $congressman;
        return $this;
    }
}
