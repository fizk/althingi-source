<?php

namespace Althingi\Controller\Cli;

use Althingi\Service\CommitteeSitting;
use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceCommitteeSittingAwareInterface
};
use Althingi\Presenters\IndexableCommitteeSittingPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;

class IndexerCommitteeSittingController implements ServiceCommitteeSittingAwareInterface, EventsAwareInterface
{
    use EventService;

    private CommitteeSitting $committeeSittingService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);
        $congressmanId = $request->getAttribute('congressman_id', null);
        $committeeId = $request->getAttribute('committee_id', null);

        /** @var \Althingi\Model\CommitteeSitting $model */
        foreach (
            $this->committeeSittingService->fetchAllGenerator(
                $assemblyId,
                $congressmanId,
                $committeeId
            ) as $model
        ) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableCommitteeSittingPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setCommitteeSitting(CommitteeSitting $committeeSitting): self
    {
        $this->committeeSittingService = $committeeSitting;
        return $this;
    }
}
