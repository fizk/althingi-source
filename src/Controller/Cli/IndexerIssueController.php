<?php

namespace Althingi\Controller\Cli;

use Althingi\Service\Issue;
use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Presenters\IndexableIssuePresenter;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceIssueAwareInterface,
};
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;

class IndexerIssueController implements ServiceIssueAwareInterface, EventsAwareInterface
{
    use EventService;

    private Issue $issueService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);

        foreach ($this->issueService->fetchAllGenerator($assemblyId) as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableIssuePresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setIssueService(Issue $issue): static
    {
        $this->issueService = $issue;
        return $this;
    }
}
