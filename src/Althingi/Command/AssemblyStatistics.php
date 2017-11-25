<?php
/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/2/17
 * Time: 8:52 AM
 */

namespace Althingi\Command;

use Althingi\Lib\CacheAwareInterface;
use Althingi\Lib\DateAndCountSequence;
use Althingi\Lib\ServiceAssemblyAwareInterface;
use Althingi\Lib\ServiceCategoryAwareInterface;
use Althingi\Lib\ServiceElectionAwareInterface;
use Althingi\Lib\ServiceIssueAwareInterface;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Lib\ServiceSpeechAwareInterface;
use Althingi\Lib\ServiceVoteAwareInterface;
use Althingi\Model\AssemblyStatusProperties;
use Althingi\Model\ModelInterface;
use Althingi\Service\Assembly;
use Althingi\Service\Category;
use Althingi\Service\Election;
use Althingi\Service\Issue;
use Althingi\Service\Party;
use Althingi\Service\Speech;
use Althingi\Service\Vote;
use Zend\Cache\Storage\StorageInterface;

class AssemblyStatistics implements
    CommandInterface,
    CacheAwareInterface,
    ServiceAssemblyAwareInterface,
    ServiceIssueAwareInterface,
    ServiceVoteAwareInterface,
    ServiceSpeechAwareInterface,
    ServicePartyAwareInterface,
    ServiceCategoryAwareInterface,
    ServiceElectionAwareInterface
{

    /** @var \Althingi\Service\Assembly */
    private $assemblyService;

    /** @var  \Althingi\Service\Category */
    private $categoryService;

    /** @var  \Althingi\Service\Election */
    private $electionService;

    /** @var  \Althingi\Service\Issue */
    private $issueService;

    /** @var  \Althingi\Service\Party */
    private $partyService;

    /** @var  \Althingi\Service\Speech */
    private $speechService;

    /** @var  \Althingi\Service\Vote */
    private $voteService;

    /** @var  \Zend\Cache\Storage\StorageInterface */
    private $storage;

    /** @var  int */
    private $assemblyId;

    /**
     * @param int $assemblyId
     * @return AssemblyStatistics
     */
    public function setAssemblyId(int $assemblyId): AssemblyStatistics
    {
        $this->assemblyId = $assemblyId;
        return $this;
    }

    /**
     * @return ModelInterface
     */
    public function exec(): ModelInterface
    {
        $assembly = $this->assemblyService->get($this->assemblyId);

        if (($item = $this->getStorage()->getItem("assembly-statistics-{$assembly->getAssemblyId()}")) !== null) {
            return unserialize($item);
        } else {
            $result = (new AssemblyStatusProperties())
                ->setBills($this->issueService->fetchNonGovernmentBillStatisticsByAssembly($assembly->getAssemblyId()))
                ->setGovernmentBills(
                    $this->issueService->fetchGovernmentBillStatisticsByAssembly($assembly->getAssemblyId())
                )
                ->setTypes($this->issueService->fetchStateByAssembly($assembly->getAssemblyId()))
                ->setVotes(DateAndCountSequence::buildDateRange(
                    $assembly->getFrom(),
                    $assembly->getTo(),
                    $this->voteService->fetchFrequencyByAssembly($assembly->getAssemblyId())
                ))
                ->setSpeeches(DateAndCountSequence::buildDateRange(
                    $assembly->getFrom(),
                    $assembly->getTo(),
                    $this->speechService->fetchFrequencyByAssembly($assembly->getAssemblyId())
                ))
                ->setPartyTimes($this->partyService->fetchTimeByAssembly($assembly->getAssemblyId()))
                ->setCategories($this->categoryService->fetchByAssembly($assembly->getAssemblyId())) //@todo remove this
                ->setElection($this->electionService->getByAssembly($assembly->getAssemblyId()))
                ->setElectionResults($this->partyService->fetchElectedByAssembly($assembly->getAssemblyId()))
            ;

            $this->getStorage()->addItem("assembly-statistics-{$assembly->getAssemblyId()}", serialize($result));
            return $result;
        }
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
     * @param Assembly $assembly
     */
    public function setAssemblyService(Assembly $assembly)
    {
        $this->assemblyService = $assembly;
    }

    /**
     * @param \Zend\Cache\Storage\StorageInterface $storage
     * @return mixed
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
