<?php

namespace Althingi\Controller\Cli;

use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceVoteItemAwareInterface,
};
use Althingi\Presenters\IndexableVoteItemPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;
use Althingi\Service\VoteItem;

class IndexerDocumentVoteItemController implements ServiceVoteItemAwareInterface, EventsAwareInterface
{
    use EventService;

    private VoteItem $voteItemService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);
        $issueId = $request->getAttribute('issue_id', null);
        $documentId = $request->getAttribute('document_id', null);

        foreach ($this->voteItemService->fetchAllGenerator($assemblyId, $issueId, $documentId) as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableVoteItemPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setVoteItemService(VoteItem $voteItem): static
    {
        $this->voteItemService = $voteItem;
        return $this;
    }
}
