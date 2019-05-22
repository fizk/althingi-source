<?php

namespace Althingi\Controller\Aggregate;

use Althingi\Injector\ServiceCategoryAwareInterface;
use Althingi\Injector\ServiceSuperCategoryAwareInterface;
use Althingi\Service\Category;
use Althingi\Service\SuperCategory;
use Rend\Controller\AbstractRestfulController;
use Rend\Helper\Http\Range;
use Rend\View\Model\CollectionModel;

class IssueCategoryController extends AbstractRestfulController implements
    ServiceCategoryAwareInterface,
    ServiceSuperCategoryAwareInterface
{
    use Range;

    /** @var $issueService \Althingi\Service\Category */
    private $categoryService;

    /** @var $issueService \Althingi\Service\SuperCategory */
    private $superCategoryService;

    /**
     * @return CollectionModel
     * @output \Althingi\Model\Category[]
     */
    public function fetchCategoriesAction()
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);

        return (new CollectionModel($this->categoryService->fetchByAssemblyAndIssue($assemblyId, $issueId)));
    }

    /**
     * @return CollectionModel
     * @output \Althingi\Model\SuperCategory[]
     */
    public function fetchSuperCategoriesAction()
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);

        return (new CollectionModel($this->superCategoryService->fetchByIssue($assemblyId, $issueId)));
    }

    /**
     * @param \Althingi\Service\Category $category
     * @return $this
     */
    public function setCategoryService(Category $category)
    {
        $this->categoryService = $category;
        return $this;
    }

    /**
     * @param SuperCategory $superCategory
     * @return $this
     */
    public function setSuperCategoryService(SuperCategory $superCategory)
    {
        $this->superCategoryService = $superCategory;
        return $this;
    }
}
