<?php

namespace Althingi\Controller\Cli;

use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Presenters\IndexableIssueCategoryPresenter;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceIssueCategoryAwareInterface,
};
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};

use Althingi\Service\EventService;
use Althingi\Service\IssueCategory;

class IndexerIssueCategoryController implements ServiceIssueCategoryAwareInterface, EventsAwareInterface
{
    use EventService;
    private IssueCategory $issueCategoryService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);

        /** @var \Althingi\Model\IssueCategory $model */
        foreach ($this->issueCategoryService->fetchAllGenerator($assemblyId) as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableIssueCategoryPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setIssueCategoryService(IssueCategory $issueCategory): self
    {
        $this->issueCategoryService = $issueCategory;
        return $this;
    }
}
