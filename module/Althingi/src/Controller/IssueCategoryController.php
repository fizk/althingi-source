<?php

namespace Althingi\Controller;

use Althingi\Form;
use Althingi\Injector\ServiceCategoryAwareInterface;
use Althingi\Injector\ServiceIssueCategoryAwareInterface;
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
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $categoryId = $this->params('category_id');

        $category = $this->categoryService
            ->fetchByAssemblyIssueAndCategory($assemblyId, $issueId, $categoryId);

        return $category
            ? (new ItemModel($category))->setStatus(200)
            : (new ErrorModel('Resource Not Found'))->setStatus(404);
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Category[]
     * @206 Success
     */
    public function getList()
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');

        $categories = $this->categoryService
            ->fetchByAssemblyAndIssue($assemblyId, $issueId);

        return (new CollectionModel($categories))
            ->setStatus(206)
            ->setRange(0, count($categories), count($categories));
    }

    /**
     * Save one issue.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\IssueCategory
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $categoryId = $this->params('category_id');

        $form = (new Form\IssueCategory())
            ->setData(array_merge(
                $data,
                [
                    'assembly_id' => $assemblyId,
                    'issue_id' => $issueId,
                    'category_id' => $categoryId,
                    'category' => 'A'
                ]
            ));

        if ($form->isValid()) {
            $affectedRows = $this->issueCategoryService->save($form->getObject());
            return (new EmptyModel())->setStatus($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\IssueCategory
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $categoryId = $this->params('category_id');

        if (($issueCategory = $this->issueCategoryService->get($assemblyId, $issueId, $categoryId)) != null) {
            $form = new Form\IssueCategory();
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

        return (new ErrorModel('Resource Not Found'))
            ->setStatus(404);
    }

    /**
     * @param IssueCategory $issueCategory
     * @return $this
     */
    public function setIssueCategoryService(IssueCategory $issueCategory)
    {
        $this->issueCategoryService = $issueCategory;
        return $this;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function setCategoryService(Category $category)
    {
        $this->categoryService = $category;
        return $this;
    }
}
