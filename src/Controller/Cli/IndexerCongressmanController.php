<?php

namespace Althingi\Controller\Cli;

use Althingi\Service\Congressman;
use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceCongressmanAwareInterface,
};
use Althingi\Presenters\IndexableCongressmanPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;

class IndexerCongressmanController implements ServiceCongressmanAwareInterface, EventsAwareInterface
{
    use EventService;

    private Congressman $congressman;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);

        /** @var \Althingi\Model\Congressman $model */
        foreach ($this->congressman->fetchAllGenerator($assemblyId) as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableCongressmanPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setCongressmanService(Congressman $congressman): self
    {
        $this->congressman = $congressman;
        return $this;
    }
}
