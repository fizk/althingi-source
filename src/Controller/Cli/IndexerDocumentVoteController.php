<?php

namespace Althingi\Controller\Cli;

use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceVoteAwareInterface,
};
use Althingi\Presenters\IndexableVotePresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;
use Althingi\Service\Vote;

class IndexerDocumentVoteController implements ServiceVoteAwareInterface, EventsAwareInterface
{
    use EventService;

    private Vote $voteService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);
        $issueId = $request->getAttribute('issue_id', null);
        $documentId = $request->getAttribute('document_id', null);

        /** @var \Althingi\Model\Vote $model */
        foreach ($this->voteService->fetchAllGenerator($assemblyId, $issueId, $documentId) as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableVotePresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setVoteService(Vote $vote): self
    {
        $this->voteService = $vote;
        return $this;
    }
}
