<?php

namespace Althingi\Controller\Cli;

use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceDocumentAwareInterface,
};
use Althingi\Presenters\IndexableDocumentPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};

use Althingi\Service\EventService;
use Althingi\Service\Document;

class IndexerDocumentController implements ServiceDocumentAwareInterface, EventsAwareInterface
{
    use EventService;
    private Document $documentService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);
        $issueId = $request->getAttribute('issue_id', null);

        /** @var \Althingi\Model\Document $model */
        foreach ($this->documentService->fetchAllGenerator($assemblyId, $issueId) as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableDocumentPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setDocumentService(Document $document): self
    {
        $this->documentService = $document;
        return $this;
    }
}
