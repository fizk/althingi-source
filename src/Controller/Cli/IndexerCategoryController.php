<?php

namespace Althingi\Controller\Cli;

use Althingi\Service\{Category, SuperCategory};
use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Presenters\{IndexableCategoryPresenter, IndexableSuperCategoryPresenter};
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceCategoryAwareInterface,
    ServiceSuperCategoryAwareInterface
};
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;

class IndexerCategoryController implements
    ServiceCategoryAwareInterface,
    ServiceSuperCategoryAwareInterface,
    EventsAwareInterface
{
    use EventService;

    private Category $categoryService;
    private SuperCategory $superCategory;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var \Althingi\Model\SuperCategory $model */
        foreach ($this->superCategory->fetchAllGenerator() as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableSuperCategoryPresenter($model), ['rows' => 1]),
            );
        }

        /** @var \Althingi\Model\Category $model */
        foreach ($this->categoryService->fetchAllGenerator() as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableCategoryPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setCategoryService(Category $category): self
    {
        $this->categoryService = $category;
        return $this;
    }

    public function setSuperCategoryService(SuperCategory $superCategory): self
    {
        $this->superCategory = $superCategory;
        return $this;
    }
}
