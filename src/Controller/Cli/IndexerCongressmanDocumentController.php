<?php

namespace Althingi\Controller\Cli;

use Althingi\Service\CongressmanDocument;
use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceCongressmanDocumentAwareInterface
};
use Althingi\Presenters\IndexableCongressmanDocumentPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};

use Althingi\Service\EventService;

class IndexerCongressmanDocumentController implements ServiceCongressmanDocumentAwareInterface, EventsAwareInterface
{
    use EventService;
    private CongressmanDocument $congressmanDocument;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);
        $congressmanId = $request->getAttribute('congressman_id', null);
        $issueId = $request->getAttribute('issue_id', null);

        /** @var \Althingi\Model\CongressmanDocument $model */
        foreach ($this->congressmanDocument->fetchAllGenerator($congressmanId, $assemblyId, $issueId) as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableCongressmanDocumentPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setCongressmanDocumentService(CongressmanDocument $congressmanDocument): self
    {
        $this->congressmanDocument = $congressmanDocument;
        return $this;
    }
}
