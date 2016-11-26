<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

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

    public function getList()
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');

        $categories = $this->categoryService
            ->fetchByAssemblyAndIssue($assemblyId, $issueId);
        
        return (new CollectionModel($categories));
    }

    /**
     * Save one issue.
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
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
            $this->issueCategoryService->create($form->getObject());
            return (new EmptyModel())->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

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
                    ->setStatus(205)
                    ->setOption('Access-Control-Allow-Origin', '*');
            }

            return (new ErrorModel($form))
                ->setStatus(400)
                ->setOption('Access-Control-Allow-Origin', '*');
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
