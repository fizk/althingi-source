<?php

namespace Althingi\Controller\Cli;

use Althingi\Service\Speech;
use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Presenters\IndexableAssemblyPresenter;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceSpeechAwareInterface
};
use Althingi\Presenters\IndexableSpeechPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;

class IndexerSpeechController implements ServiceSpeechAwareInterface, EventsAwareInterface
{
    use EventService;

    private Speech $speechService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);
        $issueId = $request->getAttribute('issue_id', null);

        /** @var \Althingi\Model\Speech $model */
        foreach ($this->speechService->fetchAllGenerator($assemblyId, $issueId) as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableSpeechPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setSpeechService(Speech $speech): self
    {
        $this->speechService = $speech;
        return $this;
    }
}
