<?php

namespace Althingi\Controller\Cli;

use Althingi\Service\MinisterSitting;
use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceMinisterSittingAwareInterface
};
use Althingi\Presenters\IndexableMinisterSittingPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;

class IndexerMinisterSittingController implements ServiceMinisterSittingAwareInterface, EventsAwareInterface
{
    use EventService;

    private MinisterSitting $ministerSittingService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);

        /** @var \Althingi\Model\MinisterSitting $model */
        foreach ($this->ministerSittingService->fetchAllGenerator($assemblyId) as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableMinisterSittingPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setMinisterSittingService(MinisterSitting $ministerSitting): static
    {
        $this->ministerSittingService = $ministerSitting;
        return $this;
    }
}
