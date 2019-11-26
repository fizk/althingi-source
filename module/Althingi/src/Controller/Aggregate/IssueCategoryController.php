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
     * @206 Success
     */
    public function fetchCategoriesAction()
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);

        $categories = $this->categoryService->fetchByAssemblyAndIssue($assemblyId, $issueId);
        return (new CollectionModel($categories))
            ->setStatus(206)
            ->setRange(0, count($categories), count($categories));
    }

    /**
     * @return CollectionModel
     * @output \Althingi\Model\SuperCategory[]
     * @206 Success
     */
    public function fetchSuperCategoriesAction()
    {
        $assemblyId = $this->params('assembly_id', null);
        $issueId = $this->params('issue_id', null);

        $superCategories = $this->superCategoryService->fetchByIssue($assemblyId, $issueId);
        return (new CollectionModel($superCategories))
            ->setStatus(206)
            ->setRange(0, count($superCategories), count($superCategories));
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
