<?php

namespace Althingi\Controller\Console;

use Althingi\Injector\LoggerAwareInterface;
use Althingi\Injector\QueueAwareInterface;
use Althingi\Injector\ServiceAssemblyAwareInterface;
use Althingi\Injector\ServiceDocumentAwareInterface;
use Althingi\Injector\ServiceIssueAwareInterface;
use Althingi\Injector\ServiceIssueCategoryAwareInterface;
use Althingi\Injector\ServiceMinisterSittingAwareInterface;
use Althingi\Injector\ServiceProponentAwareInterface;
use Althingi\Injector\ServiceSessionAwareInterface;
use Althingi\Injector\ServiceSpeechAwareInterface;
use Althingi\Injector\ServiceVoteAwareInterface;
use Althingi\Injector\ServiceVoteItemAwareInterface;
use Althingi\Injector\StallingAwareInterface;
use Althingi\Presenters\IndexableAssemblyPresenter;
use Althingi\Presenters\IndexableCongressmanDocumentPresenter;
use Althingi\Presenters\IndexableDocumentPresenter;
use Althingi\Presenters\IndexableIssueCategoryPresenter;
use Althingi\Presenters\IndexableIssuePresenter;
use Althingi\Presenters\IndexableMinisterSittingPresenter;
use Althingi\Presenters\IndexableSessionPresenter;
use Althingi\Presenters\IndexableSpeechPresenter;
use Althingi\Presenters\IndexableVoteItemPresenter;
use Althingi\Presenters\IndexableVotePresenter;
use Althingi\Service\Assembly;
use Althingi\Service\CongressmanDocument;
use Althingi\Service\Document;
use Althingi\Service\Issue;
use Althingi\Service\IssueCategory;
use Althingi\Service\MinisterSitting;
use Althingi\Service\Session;
use Althingi\Service\Speech;
use Althingi\Service\Vote;
use Althingi\Service\VoteItem;
use Althingi\QueueActions\Add;
use Althingi\Events\AddEvent;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Log\LoggerInterface;
use Zend\Mvc\Controller\AbstractActionController;

class IndexerIssueController extends AbstractActionController implements
    ServiceSpeechAwareInterface,
    ServiceIssueAwareInterface,
    ServiceIssueCategoryAwareInterface,
    ServiceDocumentAwareInterface,
    ServiceProponentAwareInterface,
    ServiceVoteAwareInterface,
    ServiceAssemblyAwareInterface,
    ServiceVoteItemAwareInterface,
    ServiceSessionAwareInterface,
    ServiceMinisterSittingAwareInterface,
    LoggerAwareInterface,
    QueueAwareInterface,
    StallingAwareInterface
{
    /** @var  \Althingi\Service\Speech */
    private $speechService;

    /** @var  \Althingi\Service\Issue */
    private $issueService;

    /** @var  \Althingi\Service\IssueCategory */
    private $issueCategoryService;

    /** @var  \Althingi\Service\Document */
    private $documentService;

    /** @var  \Althingi\Service\CongressmanDocument */
    private $congressmanDocumentService;

    /** @var  \Althingi\Service\Vote */
    private $voteService;

    /** @var  \Althingi\Service\VoteItem */
    private $voteItemService;

    /** @var  \Althingi\Service\Assembly */
    private $assemblyService;

    /** @var  \Althingi\Service\Session */
    private $sessionService;

    /** @var  \Althingi\Service\MinisterSitting */
    private $ministerSittingService;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var int */
    private $sleeptime = 150000;

    /** @var \PhpAmqpLib\Connection\AMQPStreamConnection  */
    protected $client;

    /**
     *
     */
    public function issueAction()
    {
        $assemblyId = $this->params('assembly');
        $issueId = $this->params('issue');
        $category = $this->params('category');

        $issue = $this->issueService->get($issueId, $assemblyId, $category);
        $this->indexIssue($issue);
    }

    public function assemblyAction()
    {
        $assemblyId = $this->params('assembly');
        $assembly = $this->assemblyService->get($assemblyId);

        $addEvent = new Add($this->getQueue(), $this->getLogger());
        $addEvent(new AddEvent(new IndexableAssemblyPresenter($assembly), ['rows' => 1]));
        usleep($this->sleeptime);

        $issues = $this->issueService->fetchAllByAssembly($assembly->getAssemblyId());
        foreach ($issues as $issue) {
            $this->indexIssue($issue);
        }
    }

    public function sessionAction()
    {
        $assemblyId = $this->params('assembly');

        $sessions = $this->sessionService->fetchByAssembly($assemblyId);

        $addEvent = new Add($this->getQueue(), $this->getLogger());
        foreach ($sessions as $session) {
            $addEvent(new AddEvent(new IndexableSessionPresenter($session), ['rows' => 1]));
            usleep($this->sleeptime);
        }
    }

    public function ministrySittingAction()
    {
        $assemblyId = $this->params('assembly');

        $sessions = $this->ministerSittingService->fetchByAssembly($assemblyId);

        $addEvent = new Add($this->getQueue(), $this->getLogger());
        foreach ($sessions as $session) {
            $addEvent(new AddEvent(new IndexableMinisterSittingPresenter($session), ['rows' => 1]));
            usleep($this->sleeptime);
        }
    }

    private function indexIssue(\Althingi\Model\Issue $issue)
    {
        $addEvent = new Add($this->getQueue(), $this->getLogger());
        $addEvent(new AddEvent(new IndexableIssuePresenter($issue), ['rows' => 1]));
        usleep($this->sleeptime);

        $categories = $this->issueCategoryService->fetchByIssue($issue->getAssemblyId(), $issue->getIssueId());
        foreach ($categories as $category) {
            $addEvent(new AddEvent(new IndexableIssueCategoryPresenter($category), ['rows' => 1]));
            usleep($this->sleeptime);
        }

        if ($issue->isA()) {
            $documents = $this->documentService->fetchByIssue($issue->getAssemblyId(), $issue->getIssueId());
            foreach ($documents as $document) {
                $addEvent(new AddEvent(new IndexableDocumentPresenter($document), ['rows' => 1]));
                usleep($this->sleeptime);

                $proponents = $this->congressmanDocumentService
                    ->fetchByDocument($issue->getAssemblyId(), $issue->getIssueId(), $document->getDocumentId());
                foreach ($proponents as $proponent) {
                    $addEvent(new AddEvent(new IndexableCongressmanDocumentPresenter($proponent), ['rows' => 1]));
                    usleep($this->sleeptime);
                }

                $votes = $this->voteService
                    ->fetchByDocument($issue->getAssemblyId(), $issue->getIssueId(), $document->getDocumentId());
                foreach ($votes as $vote) {
                    $addEvent(new AddEvent(new IndexableVotePresenter($vote), ['rows' => 1]));
                    usleep($this->sleeptime);

                    $voteItems = $this->voteItemService->fetchByVote($vote->getVoteId());
                    foreach ($voteItems as $voteItem) {
                        $addEvent(new AddEvent(new IndexableVoteItemPresenter($voteItem), ['rows' => 1]));
                        usleep($this->sleeptime);
                    }
                }
            }
        }

        $speeches = $this->speechService
            ->fetchAllByIssue($issue->getAssemblyId(), $issue->getIssueId(), $issue->getCategory());
        foreach ($speeches as $speech) {
            $addEvent(new AddEvent(new IndexableSpeechPresenter($speech), ['rows' => 1]));
            usleep($this->sleeptime);
        }
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
     * @param \Althingi\Service\Issue $issue
     * @return $this
     */
    public function setIssueService(Issue $issue)
    {
        $this->issueService = $issue;
        return $this;
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }


    public function setQueue(AMQPStreamConnection $connection)
    {
        $this->client = $connection;
        return $this;
    }

    public function getQueue(): AMQPStreamConnection
    {
        return $this->client;
    }

    /**
     * @param IssueCategory $issueCategory
     * @return $this;
     */
    public function setIssueCategoryService(IssueCategory $issueCategory)
    {
        $this->issueCategoryService = $issueCategory;
        return $this;
    }

    /**
     * @param Document $document
     * @return $this;
     */
    public function setDocumentService(Document $document)
    {
        $this->documentService = $document;
        return $this;
    }

    /**
     * @param CongressmanDocument $congressmanDocument
     * @return $this;
     */
    public function setCongressmanDocumentService(CongressmanDocument $congressmanDocument)
    {
        $this->congressmanDocumentService = $congressmanDocument;
        return $this;
    }

    /**
     * @param \Althingi\Service\Vote $vote
     * @return $this;
     */
    public function setVoteService(Vote $vote)
    {
        $this->voteService = $vote;
        return $this;
    }

    /**
     * @param VoteItem $voteItem
     * @return $this;
     */
    public function setVoteItemService(VoteItem $voteItem)
    {
        $this->voteItemService = $voteItem;
        return $this;
    }

    /**
     * @param \Althingi\Service\Assembly $assembly
     * @return $this;
     */
    public function setAssemblyService(Assembly $assembly)
    {
        $this->assemblyService = $assembly;
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
     * @param \Althingi\Service\MinisterSitting $ministerSitting
     * @return $this
     */
    public function setMinisterSittingService(MinisterSitting $ministerSitting)
    {
        $this->ministerSittingService = $ministerSitting;
        return $this;
    }

    /**
     * @param int $time
     * @return $this
     */
    public function setStallTime(int $time)
    {
        $this->sleeptime = $time;
        return $this;
    }
}
