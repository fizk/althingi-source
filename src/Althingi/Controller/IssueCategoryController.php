<?php

namespace Althingi\Controller;

use Althingi\Form\IssueCategory as IssueCategoryForm;
use Althingi\Lib\ServiceCategoryAwareInterface;
use Althingi\Lib\ServiceIssueCategoryAwareInterface;
use Althingi\Service\Category;
use Althingi\Service\IssueCategory;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ItemModel;


class IssueCategoryController extends AbstractRestfulController implements
    ServiceIssueCategoryAwareInterface,
    ServiceCategoryAwareInterface
{
    /**
     * @var \Althingi\Service\IssueCategory
     */
    private $issueCategoryService;

    /**
     * @var \Althingi\Service\Category
     */
    private $categoryService;

    /**
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Category
     */
    public function get($id)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $categoryId = $this->params('category_id');

        $category = $this->categoryService
            ->fetchByAssemblyIssueAndCategory($assemblyId, $issueId, $categoryId);

        return $category
            ? (new ItemModel($category))
            : $this->notFoundAction() ;
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Category[]
     */
    public function getList()
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');

        $categories = $this->categoryService
            ->fetchByAssemblyAndIssue($assemblyId, $issueId);
        $categoriesCount = count($categories);

        return (new CollectionModel($categories))
            ->setStatus(206)
            ->setRange(0, $categoriesCount, $categoriesCount);
    }

    /**
     * Save one issue.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\IssueCategory
     */
    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $categoryId = $this->params('category_id');

        $form = (new IssueCategoryForm())
            ->setData(array_merge(
                $data,
                ['assembly_id' => $assemblyId, 'issue_id' => $issueId, 'category_id' => $categoryId]
            ));

        if ($form->isValid()) {
            $affectedRows = $this->issueCategoryService->save($form->getObject());
            return (new EmptyModel())->setStatus($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\IssueCategory
     */
    public function patch($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $categoryId = $this->params('category_id');

        if (($issueCategory = $this->issueCategoryService->get($assemblyId, $issueId, $categoryId)) != null) {
            $form = new IssueCategoryForm();
            $form->bind($issueCategory);
            $form->setData($data);

            if ($form->isValid()) {
                $this->issueCategoryService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * @param IssueCategory $issueCategory
     */
    public function setIssueCategoryService(IssueCategory $issueCategory)
    {
        $this->issueCategoryService = $issueCategory;
    }

    /**
     * @param Category $category
     */
    public function setCategoryService(Category $category)
    {
        $this->categoryService = $category;
    }
}
