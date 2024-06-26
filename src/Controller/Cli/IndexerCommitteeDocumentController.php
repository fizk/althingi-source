<?php

namespace Althingi\Controller\Cli;

use Althingi\Service\CommitteeDocument;
use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceCommitteeDocumentAwareInterface
};
use Althingi\Presenters\IndexableCommitteeDocumentPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;

class IndexerCommitteeDocumentController implements ServiceCommitteeDocumentAwareInterface, EventsAwareInterface
{
    use EventService;

    private CommitteeDocument $serviceCommitteeDocument;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);
        $congressmanId = $request->getAttribute('congressman_id', null);
        $documentId = $request->getAttribute('document_id', null);

        /** @var \Althingi\Model\CommitteeDocument $model */
        foreach (
            $this->serviceCommitteeDocument->fetchAllGenerator(
                $congressmanId,
                $assemblyId,
                $documentId
            ) as $model
        ) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableCommitteeDocumentPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setCommitteeDocumentService(CommitteeDocument $committeeDocument): static
    {
        $this->serviceCommitteeDocument = $committeeDocument;
        return $this;
    }
}
