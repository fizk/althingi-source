<?php

namespace Althingi\Controller\Cli;

use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceSessionAwareInterface
};
use Althingi\Presenters\IndexableSessionPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;
use Althingi\Service\Session;

class IndexerSessionController implements ServiceSessionAwareInterface, EventsAwareInterface
{
    use EventService;

    private Session $session;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);
        $congressmanId = $request->getAttribute('congressman_id', null);

        /** @var \Althingi\Model\Session $model */
        foreach ($this->session->fetchAllGenerator($assemblyId, $congressmanId) as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableSessionPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setSessionService(Session $session): self
    {
        $this->session = $session;
        return $this;
    }
}
